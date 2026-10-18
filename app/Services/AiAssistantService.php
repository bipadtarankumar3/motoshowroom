<?php

namespace App\Services;

use App\Enums\BikeCategory;
use App\Models\AiAssistantSetting;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Bike;
use App\Models\Enquiry;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AiAssistantService
{
    /**
     * Process an incoming customer message, determine intent, generate intelligent reply and record chat.
     *
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public function processMessage(Showroom $showroom, string $sessionId, string $userMessage, ?User $user = null, array $metadata = []): array
    {
        // 1. Resolve or create conversation
        $conversation = AiConversation::firstOrCreate([
            'tenant_id' => $showroom->id,
            'session_id' => $sessionId,
        ], [
            'user_id' => $user?->id,
            'customer_name' => $metadata['customer_name'] ?? null,
            'customer_email' => $metadata['customer_email'] ?? null,
            'customer_phone' => $metadata['customer_phone'] ?? null,
        ]);

        // Update customer details if newly provided
        $updateFields = [];
        if (! empty($metadata['customer_name']) && empty($conversation->customer_name)) {
            $updateFields['customer_name'] = trim((string) $metadata['customer_name']);
        }
        if (! empty($metadata['customer_email']) && empty($conversation->customer_email)) {
            $updateFields['customer_email'] = trim((string) $metadata['customer_email']);
        }
        if (! empty($metadata['customer_phone']) && empty($conversation->customer_phone)) {
            $updateFields['customer_phone'] = trim((string) $metadata['customer_phone']);
        }
        if (! empty($updateFields)) {
            $conversation->update($updateFields);
        }

        // 2. Record incoming user message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'tenant_id' => $showroom->id,
            'sender' => 'user',
            'message' => $userMessage,
        ]);

        // 3. Load showroom context
        $setting = $showroom->getOrCreateAiSetting();
        $trimmed = trim($userMessage);
        $lower = strtolower($trimmed);

        $detectedIntent = 'general';
        $structuredPayload = null;
        $replyText = '';

        // Check A: Custom FAQs added by Showroom Staff
        $matchedFaq = $this->matchCustomFaq($setting, $lower);
        if ($matchedFaq) {
            $detectedIntent = 'custom_faq';
            $replyText = $matchedFaq;
        }

        // Check B: Automatic Lead Conversion / Test Ride Scheduling
        if (! $replyText && $this->isTestRideIntent($lower)) {
            $detectedIntent = 'test_ride';
            $extractedPhone = $this->extractPhoneNumber($userMessage) ?? $conversation->customer_phone;
            $extractedName = $metadata['customer_name'] ?? $conversation->customer_name ?? 'Showroom Visitor';

            if (! empty($extractedPhone)) {
                // Auto convert into PurchaseEnquiry record
                $enquiry = Enquiry::create([
                    'tenant_id' => $showroom->id,
                    'user_id' => $user?->id,
                    'bike_id' => $metadata['bike_id'] ?? null,
                    'enquiry_number' => 'ENQ-'.date('Ymd').'-'.strtoupper(Str::random(4)),
                    'type' => 'test_ride',
                    'customer_name' => $extractedName,
                    'customer_email' => $conversation->customer_email ?? "lead-{$conversation->id}@visitor.example",
                    'customer_phone' => $extractedPhone,
                    'preferred_date' => now()->addDay()->toDateString(),
                    'preferred_time_slot' => '11:00 AM - 01:00 PM',
                    'status' => 'new',
                    'customer_notes' => "Captured via AI Virtual Assistant chat: '{$trimmed}'",
                ]);

                $conversation->update([
                    'lead_converted' => true,
                    'enquiry_id' => $enquiry->id,
                    'customer_phone' => $extractedPhone,
                ]);

                $replyText = "Fantastic! I've booked your test ride request with reference #{$enquiry->enquiry_number}. Our sales advisors at {$showroom->name} will call you shortly on {$extractedPhone} to confirm your appointment.";
                $structuredPayload = [
                    'type' => 'test_ride_confirmed',
                    'enquiry_number' => $enquiry->enquiry_number,
                    'customer_name' => $extractedName,
                ];
            } else {
                $replyText = 'We would love to get you in the saddle for a test ride! Please share your phone number, or click the button below to choose your preferred motorcycle and schedule your session.';
                $structuredPayload = [
                    'type' => 'test_ride_prompt',
                    'booking_url' => route('showrooms.enquiries.create', $showroom),
                ];
            }
        }

        // Check C: Workshop & Service Booking
        if (! $replyText && $this->isServiceIntent($lower)) {
            $detectedIntent = 'service_inquiry';
            $isOpen = $showroom->isOpenNow();
            $statusText = $isOpen ? 'currently open' : 'currently closed';

            $replyText = "Our workshop and service center at {$showroom->name} is {$statusText}. We offer comprehensive motorcycle maintenance, brake overhauls, engine tuning, and express servicing. You can book an appointment or track ongoing repairs online.";
            $structuredPayload = [
                'type' => 'service_options',
                'book_url' => route('showrooms.services.create', $showroom),
                'track_url' => route('showrooms.services.track', $showroom),
            ];
        }

        // Check D: Promotional Campaigns & Scratch Cards
        if (! $replyText && $this->isPromotionsIntent($lower)) {
            $detectedIntent = 'promotions';
            $activePromos = $showroom->activePromotionalCampaigns()->with('prizes')->take(3)->get();

            if ($activePromos->isNotEmpty()) {
                $firstPromo = $activePromos->first();
                $replyText = "We have exciting rewards happening right now! Check out our '{$firstPromo->name}' campaign where you can scratch and win exclusive discounts, free riding gear, and accessories!";
                $structuredPayload = [
                    'type' => 'promotional_campaigns',
                    'campaigns' => $activePromos->map(fn ($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'url' => route('showrooms.promotions.show', [$showroom, $p]),
                    ]),
                ];
            } else {
                $replyText = "We don't have an active scratch card promotion today, but you can explore our latest motorcycles and book a test ride anytime!";
            }
        }

        // Check E: Showroom Timings & Location
        if (! $replyText && $this->isOperatingHoursOrLocationIntent($lower)) {
            $detectedIntent = 'general';
            $isOpen = $showroom->isOpenNow();
            $hours = $showroom->getOperatingHours();
            $today = strtolower(now()->format('l'));
            $todayHours = $hours[$today] ?? ['open' => '09:30', 'close' => '20:00', 'is_closed' => false];

            $openString = $todayHours['is_closed'] ? 'Closed today' : "{$todayHours['open']} to {$todayHours['close']}";
            $replyText = "{$showroom->name} is located at {$showroom->city}, {$showroom->state}. Today's timings are {$openString} (".($isOpen ? 'Open Now' : 'Closed Now')."). You can reach our front desk at {$showroom->phone} or email us at {$showroom->email}.";
            $structuredPayload = [
                'type' => 'showroom_info',
                'name' => $showroom->name,
                'city' => $showroom->city,
                'state' => $showroom->state,
                'phone' => $showroom->phone,
                'email' => $showroom->email,
                'is_open' => $isOpen,
            ];
        }

        // Check F: Motorcycle Inventory Recommendation
        if (! $replyText) {
            $recommendations = $this->recommendMotorcycles($showroom, $lower);
            if ($recommendations['bikes']->isNotEmpty()) {
                $detectedIntent = 'bike_recommendation';
                $replyText = $recommendations['message'];
                $structuredPayload = [
                    'type' => 'bike_recommendations',
                    'bikes' => $recommendations['bikes']->map(fn (Bike $b) => [
                        'id' => $b->id,
                        'name' => $b->name,
                        'brand' => $b->brand,
                        'model_year' => $b->model_year,
                        'category' => $b->category->label(),
                        'price' => $b->formatted_price,
                        'image_url' => $b->primary_image_url,
                        'has_360' => $b->has360View(),
                        'url' => route('showrooms.bikes.show', [$showroom, $b]),
                        'test_ride_url' => route('showrooms.enquiries.create', ['showroom' => $showroom, 'bike' => $b->id]),
                    ]),
                ];
            }
        }

        // Check G: Default Greeting / Fallback
        if (! $replyText) {
            $detectedIntent = 'general';
            $replyText = "I'm here to help you explore motorcycles at {$showroom->name}! You can ask me to recommend bikes by budget (e.g., 'under 15 lakhs'), filter by category like sports or EV, book a test ride, or check our workshop timings.";
        }

        // 4. Record assistant message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'tenant_id' => $showroom->id,
            'sender' => 'assistant',
            'message' => $replyText,
            'structured_payload' => $structuredPayload,
        ]);

        // 5. Update conversation status
        $conversation->update(['detected_intent' => $detectedIntent]);

        return [
            'conversation_id' => $conversation->id,
            'detected_intent' => $detectedIntent,
            'reply' => $replyText,
            'structured_payload' => $structuredPayload,
            'lead_converted' => $conversation->lead_converted,
        ];
    }

    /**
     * Match user query against custom FAQs defined by showroom staff.
     */
    protected function matchCustomFaq(AiAssistantSetting $setting, string $query): ?string
    {
        $faqs = $setting->faqs_list;
        foreach ($faqs as $faq) {
            $question = strtolower($faq['question'] ?? '');
            if (empty($question) || empty($faq['answer'])) {
                continue;
            }

            // Word overlap check
            $qWords = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $question)));
            $matched = 0;
            foreach ($qWords as $word) {
                if (strlen($word) > 2 && str_contains($query, $word)) {
                    $matched++;
                }
            }

            if ($matched >= 2 || str_contains($query, $question)) {
                return $faq['answer'];
            }
        }

        return null;
    }

    /**
     * Detect if message is inquiring about test rides.
     */
    protected function isTestRideIntent(string $query): bool
    {
        $keywords = ['test ride', 'test-ride', 'book ride', 'schedule demo', 'demo ride', 'trial ride', 'take a ride'];
        foreach ($keywords as $kw) {
            if (str_contains($query, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if message is inquiring about service or maintenance.
     */
    protected function isServiceIntent(string $query): bool
    {
        $keywords = ['service', 'repair', 'maintenance', 'oil change', 'workshop', 'mechanic', 'diagnostic', 'brake overhaul', 'servicing'];
        foreach ($keywords as $kw) {
            if (str_contains($query, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if message is inquiring about promotions or scratch cards.
     */
    protected function isPromotionsIntent(string $query): bool
    {
        $keywords = ['promotion', 'promotions', 'scratch card', 'coupon', 'discount', 'contest', 'offer', 'offers', 'win'];
        foreach ($keywords as $kw) {
            if (str_contains($query, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if message is inquiring about operating hours or location.
     */
    protected function isOperatingHoursOrLocationIntent(string $query): bool
    {
        $keywords = ['hours', 'timing', 'timings', 'open today', 'close', 'address', 'location', 'where are you', 'contact number', 'phone number', 'email'];
        foreach ($keywords as $kw) {
            if (str_contains($query, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract phone number from text if user typed one.
     */
    protected function extractPhoneNumber(string $text): ?string
    {
        if (preg_match('/(\+?91[\-\s]?)?[6-9]\d{9}/', $text, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Intelligent motorcycle query parser and recommender.
     *
     * @return array{message: string, bikes: Collection<int, Bike>}
     */
    protected function recommendMotorcycles(Showroom $showroom, string $query): array
    {
        $bikesQuery = $showroom->publishedBikes()->with(['images', 'variants']);

        // 1. Budget extraction
        $maxPrice = null;
        if (preg_match('/(?:under|below|budget|within|upto|up to)\s*(?:of)?\s*(?:rs\.?|inr|₹)?\s*(\d+(?:\.\d+)?)\s*(lakh|lakhs|lac|lacs|k)?/i', $query, $matches)) {
            $num = (float) $matches[1];
            $multiplier = strtolower($matches[2] ?? '');
            if (str_starts_with($multiplier, 'lak') || str_starts_with($multiplier, 'lac')) {
                $maxPrice = $num * 100000;
            } elseif ($multiplier === 'k') {
                $maxPrice = $num * 1000;
            } elseif ($num < 100) { // e.g. "under 15" implies 15 lakhs
                $maxPrice = $num * 100000;
            } else {
                $maxPrice = $num;
            }
        }

        if ($maxPrice !== null) {
            $bikesQuery->where('price', '<=', $maxPrice);
        }

        // 2. Category extraction
        $matchedCategory = null;
        if (str_contains($query, 'sport') || str_contains($query, 'superbike') || str_contains($query, 'track')) {
            $matchedCategory = BikeCategory::Sports;
        } elseif (str_contains($query, 'cruiser') || str_contains($query, 'bobber')) {
            $matchedCategory = BikeCategory::Cruiser;
        } elseif (str_contains($query, 'adventure') || str_contains($query, 'adv') || str_contains($query, 'touring') || str_contains($query, 'tourer')) {
            $matchedCategory = BikeCategory::Adventure;
        } elseif (str_contains($query, 'electric') || str_contains($query, 'ev') || str_contains($query, 'battery')) {
            $matchedCategory = BikeCategory::Electric;
        } elseif (str_contains($query, 'scooter')) {
            $matchedCategory = BikeCategory::Scooter;
        } elseif (str_contains($query, 'commuter') || str_contains($query, 'mileage')) {
            $matchedCategory = BikeCategory::Commuter;
        }

        if ($matchedCategory !== null) {
            $bikesQuery->where('category', $matchedCategory->value);
        }

        // 3. Brand extraction
        $brands = ['ducati', 'bmw', 'kawasaki', 'yamaha', 'honda', 'triumph', 'harley', 'ktm', 'suzuki', 'aprilia'];
        foreach ($brands as $b) {
            if (str_contains($query, $b)) {
                $bikesQuery->where('brand', 'LIKE', "%{$b}%");
                break;
            }
        }

        $bikes = $bikesQuery->orderBy('price', 'desc')->take(3)->get();

        if ($bikes->isEmpty()) {
            // Fallback: If strict filters yielded 0, give featured bikes
            $bikes = $showroom->publishedBikes()->with(['images'])->orderByDesc('is_featured')->take(3)->get();
            if ($bikes->isEmpty()) {
                return ['message' => '', 'bikes' => collect()];
            }

            return [
                'message' => "I couldn't find an exact match for your query, but here are our top featured machines in stock right now at {$showroom->name}:",
                'bikes' => $bikes,
            ];
        }

        $count = $bikes->count();
        $bikeList = $bikes->map(fn ($b) => "{$b->name} ({$b->formatted_price})")->join(', ', ', and ');
        $message = "I found {$count} fantastic motorcycle".($count > 1 ? 's' : '')." matching your preferences, including the {$bikeList}. Check out the details below:";

        return [
            'message' => $message,
            'bikes' => $bikes,
        ];
    }
}
