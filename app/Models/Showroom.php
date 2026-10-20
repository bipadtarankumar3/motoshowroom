<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showroom extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'custom_domain',
        'domain_verified_at',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'logo_url',
        'banner_url',
        'brand_color_primary',
        'brand_color_accent',
        'status',
        'public_website_enabled',
        'read_only_mode',
        'settings',
    ];

    /**
     * Get the route key for the model to resolve by slug by default.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'domain_verified_at' => 'datetime',
            'public_website_enabled' => 'boolean',
            'read_only_mode' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * All memberships of staff and owners for this showroom.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ShowroomMembership::class);
    }

    /**
     * All users associated with this showroom.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'showroom_memberships')
            ->withPivot('role', 'permissions', 'status')
            ->withTimestamps();
    }

    /**
     * The primary owner membership for this showroom.
     */
    public function ownerMembership(): HasOne
    {
        return $this->hasOne(ShowroomMembership::class)->where('role', 'owner');
    }

    /**
     * All motorcycle inventory belonging to this showroom.
     */
    public function bikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'tenant_id');
    }

    /**
     * Published motorcycles visible to public customers.
     */
    public function publishedBikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'tenant_id')->where('is_published', true);
    }

    /**
     * Featured motorcycles highlighted on showroom showcase.
     */
    public function featuredBikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'tenant_id')
            ->where('is_published', true)
            ->where('is_featured', true);
    }

    /**
     * All customer purchase enquiries and test ride requests.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'tenant_id');
    }

    /**
     * Active enquiries pending customer follow-up or test ride delivery.
     */
    public function activeEnquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'tenant_id')->whereIn('status', [
            'new',
            'contacted',
            'test_ride_scheduled',
            'negotiation',
            'booking_pending',
        ]);
    }

    /**
     * Custom motorcycle categories created by this showroom.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'tenant_id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Active categories for storefront display.
     */
    public function activeCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'tenant_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Hero and promotional banners created by this showroom.
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'tenant_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Active hero banners for storefront showcase.
     */
    public function activeBanners(): HasMany
    {
        return $this->hasMany(Banner::class, 'tenant_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * All job openings posted by this dealership.
     */
    public function jobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class, 'tenant_id');
    }

    /**
     * Active job vacancies available for public candidate application.
     */
    public function activeJobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class, 'tenant_id')
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('deadline')->orWhereDate('deadline', '>=', today());
            });
    }

    /**
     * All job applications submitted to this dealership.
     */
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'tenant_id');
    }

    /**
     * All promotional marketing campaigns created by this showroom.
     */
    public function promotionalCampaigns(): HasMany
    {
        return $this->hasMany(PromotionalCampaign::class, 'tenant_id');
    }

    /**
     * Alias for route model binding scoping.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(PromotionalCampaign::class, 'tenant_id');
    }

    /**
     * Active promotional campaigns currently open for participation.
     */
    public function activePromotionalCampaigns(): HasMany
    {
        return $this->hasMany(PromotionalCampaign::class, 'tenant_id')
            ->where('status', CampaignStatus::Active->value)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today());
    }

    /**
     * All promotional scratch card participations issued by this showroom.
     */
    public function scratchCardParticipations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'tenant_id');
    }

    /**
     * Alias for route model binding scoping.
     */
    public function participations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'tenant_id');
    }

    /**
     * Check if the showroom is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial'], true);
    }

    /**
     * Determine if the public website is accessible.
     */
    public function isWebsiteAccessible(): bool
    {
        return $this->public_website_enabled && $this->status !== 'suspended';
    }

    /**
     * Audit logs related to this showroom.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'tenant_id')->latest('created_at');
    }

    /**
     * Get the owner user of the showroom.
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->ownerMembership?->user;
    }

    /**
     * All subscriptions for this showroom.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest('created_at');
    }

    /**
     * The active or current subscription for this showroom.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    /**
     * Determine if showroom has access to a specific plan feature flag.
     * Evaluates showroom settings overrides, then active subscription entitlements and VIP overrides.
     */
    public function canAccessFeature(string $featureKey): bool
    {
        $showroomOverrides = $this->getSetting('feature_overrides', []);
        if (is_array($showroomOverrides) && array_key_exists($featureKey, $showroomOverrides)) {
            $val = $showroomOverrides[$featureKey];
            if ($val === true || $val === 1 || $val === '1') {
                return true;
            }
            if ($val === false || $val === 0 || $val === '0') {
                return false;
            }
        }

        return $this->subscription?->canAccessFeature($featureKey) ?? false;
    }

    /**
     * Determine if showroom has reached an entitlement limit.
     */
    public function hasReachedLimit(string $limitKey, int $currentCount): bool
    {
        $showroomOverrides = $this->getSetting('feature_overrides', []);
        if (is_array($showroomOverrides) && isset($showroomOverrides[$limitKey]) && is_numeric($showroomOverrides[$limitKey])) {
            return $currentCount >= (int) $showroomOverrides[$limitKey];
        }

        if (! $this->subscription) {
            return true;
        }

        return $this->subscription->hasReachedLimit($limitKey, $currentCount);
    }

    /**
     * Get a setting from the json settings attribute with optional fallback default.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Get the Razorpay Key ID for this showroom, or fall back to system configuration.
     */
    public function getRazorpayKeyId(): string
    {
        return (string) ($this->getSetting('razorpay_key_id') ?: config('services.razorpay.key_id', 'rzp_test_placeholder'));
    }

    /**
     * Get the Razorpay Key Secret for this showroom, or fall back to system configuration.
     */
    public function getRazorpayKeySecret(): string
    {
        return (string) ($this->getSetting('razorpay_key_secret') ?: config('services.razorpay.key_secret', 'rzp_secret_placeholder'));
    }

    /**
     * Get the Razorpay Webhook Secret for this showroom, or fall back to system configuration.
     */
    public function getRazorpayWebhookSecret(): string
    {
        return (string) ($this->getSetting('razorpay_webhook_secret') ?: config('services.razorpay.webhook_secret', 'rzp_webhook_secret_placeholder'));
    }

    /**
     * Check if this showroom has configured its own custom Razorpay API credentials.
     */
    public function hasCustomRazorpayCredentials(): bool
    {
        $keyId = $this->getSetting('razorpay_key_id');
        $keySecret = $this->getSetting('razorpay_key_secret');

        return ! empty($keyId) && ! empty($keySecret) && ! str_contains((string) $keyId, 'placeholder');
    }

    /**
     * Check if online Razorpay payments are enabled for this showroom.
     */
    public function isRazorpayOnlinePaymentEnabled(): bool
    {
        return (bool) $this->getSetting('razorpay_enabled', true);
    }

    /**
     * Get default refundable booking token amount (₹10,000 default).
     */
    public function getBookingTokenAmount(): float
    {
        return (float) $this->getSetting('booking_token_amount', 10000.0);
    }

    /**
     * Get the marketing headline or default to showroom name.
     */
    public function getHeadlineAttribute(): string
    {
        return $this->getSetting('headline', 'Welcome to '.$this->name);
    }

    /**
     * Get the marketing tagline.
     */
    public function getTaglineAttribute(): string
    {
        return $this->getSetting('tagline', 'Authorized Premium Motorcycle Dealership & Certified Service Center');
    }

    /**
     * Get the about us description story.
     */
    public function getAboutTextAttribute(): string
    {
        return $this->getSetting('about_text', "Welcome to {$this->name}. We are dedicated to providing motorcycle enthusiasts with world-class bikes, genuine parts, factory-certified service technicians, and an exceptional customer experience.");
    }

    /**
     * Get social media handles.
     *
     * @return array<string, string>
     */
    public function getSocialLinks(): array
    {
        return $this->getSetting('social_links', [
            'instagram' => '',
            'youtube' => '',
            'facebook' => '',
            'twitter' => '',
        ]);
    }

    /**
     * Get weekly operating schedule.
     *
     * @return array<string, array{open: string, close: string, is_closed: bool}>
     */
    public function getOperatingHours(): array
    {
        $default = [
            'monday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'tuesday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'wednesday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'thursday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'friday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'saturday' => ['open' => '09:30', 'close' => '20:30', 'is_closed' => false],
            'sunday' => ['open' => '10:00', 'close' => '14:00', 'is_closed' => false],
        ];

        return $this->getSetting('operating_hours', $default);
    }

    /**
     * Determine if the showroom is currently open for business.
     */
    public function isOpenNow(): bool
    {
        $today = strtolower(now()->format('l'));
        $hours = $this->getOperatingHours();

        if (! isset($hours[$today]) || ($hours[$today]['is_closed'] ?? false)) {
            return false;
        }

        $now = now()->format('H:i');
        $open = $hours[$today]['open'] ?? '09:30';
        $close = $hours[$today]['close'] ?? '20:00';

        return $now >= $open && $now <= $close;
    }

    /**
     * Custom workshop service packages configured by this showroom.
     */
    public function services(): HasMany
    {
        return $this->hasMany(ShowroomService::class, 'tenant_id');
    }

    /**
     * Active custom workshop service packages ordered by display priority.
     */
    public function activeServices(): HasMany
    {
        return $this->services()->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Seed initial customizable workshop services if none exist yet.
     */
    public function seedDefaultServicesIfNeeded(): void
    {
        if ($this->services()->exists()) {
            return;
        }

        $defaults = [
            [
                'name' => 'Periodic General Maintenance',
                'slug' => 'periodic-general-maintenance',
                'description' => 'Comprehensive 32-point inspection, spark plug service, chain cleaning & slack adjustment, filter clean, fluid top-up, and full pressure wash.',
                'price' => 1499.00,
                'estimated_duration_minutes' => 90,
                'is_payment_enabled' => true,
                'is_active' => true,
                'badge' => 'Popular',
                'features' => ['32-Point Comprehensive Inspection', 'Spark Plug Inspection & Clean', 'Chain Degrease, Slack Adjust & Lube', 'Air Filter Clean / Replace Check', 'Full Foam Wash & Polish'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Engine Oil & Filter Service',
                'slug' => 'engine-oil-filter-service',
                'description' => 'Premium synthetic engine oil replacement, OEM oil filter replacement, chain degrease & high-temp wax lubrication.',
                'price' => 799.00,
                'estimated_duration_minutes' => 45,
                'is_payment_enabled' => false,
                'is_active' => true,
                'badge' => 'Express',
                'features' => ['100% Synthetic Engine Oil', 'Genuine OEM Oil Filter', 'Chain Lubrication & Degrease', 'Tire Pressure & Tread Check'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Brake System Overhaul',
                'slug' => 'brake-system-overhaul',
                'description' => 'Front and rear brake pad inspection/replacement, DOT4 brake fluid flush & bleed, caliper pin greasing, and rotor true check.',
                'price' => 1199.00,
                'estimated_duration_minutes' => 60,
                'is_payment_enabled' => true,
                'is_active' => true,
                'badge' => 'Safety First',
                'features' => ['Brake Pads Wear Inspection', 'DOT4 Hydraulic Fluid Flush', 'Caliper Pin & Piston Degrease', 'Rotor Trueness & Alignment Check'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Performance & Engine Tuning',
                'slug' => 'performance-engine-tuning',
                'description' => 'Electronic throttle body cleaning, EFI sensor diagnostics, valve tappet clearance check, and spark ignition timing sync.',
                'price' => 2499.00,
                'estimated_duration_minutes' => 120,
                'is_payment_enabled' => true,
                'is_active' => true,
                'badge' => 'Recommended',
                'features' => ['Electronic Throttle Body Clean', 'OBD-II ECU Diagnostic Scan', 'Valve Clearance (Tappet) Adjust', 'Fuel Injector Ultrasonic Clean'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Electrical & Battery Diagnostic',
                'slug' => 'electrical-battery-diagnostic',
                'description' => 'Digital battery load testing, charging stator check, wiring harness resistance scan, and high-output LED alignment.',
                'price' => 699.00,
                'estimated_duration_minutes' => 45,
                'is_payment_enabled' => false,
                'is_active' => true,
                'badge' => 'Diagnostic',
                'features' => ['Digital Battery Cranking Load Test', 'Stator Coil Output & RR Unit Check', 'Wiring Harness Ground Check', 'Headlamp Beam Alignment'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Accident & Body Restoration',
                'slug' => 'accident-body-restoration',
                'description' => 'Structural chassis alignment, fairing/body panel repair, fork straightness verification, and OEM paint refinishing.',
                'price' => 4999.00,
                'estimated_duration_minutes' => 240,
                'is_payment_enabled' => true,
                'is_active' => true,
                'badge' => 'Specialist',
                'features' => ['Chassis & Swingarm Laser Alignment', 'Fork True & Triple Tree Calibration', 'OEM Fairing & Panel Repair', 'High-Gloss Finish Refinishing'],
                'sort_order' => 6,
            ],
        ];

        foreach ($defaults as $data) {
            $this->services()->create($data);
        }
    }

    /**
     * All service appointments booked with this showroom workshop.
     */
    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'tenant_id');
    }

    /**
     * Today's scheduled service appointments.
     */
    public function todayServiceBookings(): HasMany
    {
        return $this->serviceBookings()->whereDate('booking_date', today());
    }

    /**
     * Total physical service bays available for simultaneous vehicle maintenance.
     */
    public function getServiceBayCapacityAttribute(): int
    {
        return (int) $this->getSetting('service_bays_count', 4);
    }

    /**
     * All financial payment transactions processed for this showroom.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    /**
     * AI Virtual Assistant configuration and settings for this showroom.
     */
    public function aiSetting(): HasOne
    {
        return $this->hasOne(AiAssistantSetting::class, 'tenant_id');
    }

    /**
     * Retrieve or initialize default AI assistant settings for this showroom.
     */
    public function getOrCreateAiSetting(): AiAssistantSetting
    {
        return $this->aiSetting()->firstOrCreate([
            'tenant_id' => $this->id,
        ], [
            'bot_name' => "{$this->name} AI Concierge",
            'welcome_message' => "Hello! Welcome to {$this->name}. I am your AI motorcycle concierge. How can I help you find your dream bike or schedule a service today?",
            'tone' => 'friendly',
            'is_enabled' => true,
            'suggested_prompts' => [
                'Recommend a sports bike under ₹15 Lakhs',
                'What electric motorcycles do you have in stock?',
                'How do I schedule a test ride?',
                'What are your workshop timings today?',
                'Any active promotional discounts or scratch cards?',
            ],
            'custom_faqs' => [],
        ]);
    }

    /**
     * AI customer chat conversations.
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'tenant_id')->latest();
    }

    /**
     * Effective primary brand color with safe fallback.
     */
    public function getPrimaryColorAttribute(): string
    {
        return ! empty($this->brand_color_primary) ? $this->brand_color_primary : '#dc2626';
    }

    /**
     * Effective accent brand color with safe fallback.
     */
    public function getAccentColorAttribute(): string
    {
        return ! empty($this->brand_color_accent) ? $this->brand_color_accent : '#0f172a';
    }

    /**
     * High-contrast text color (#0f172a or #ffffff) for elements on primary brand background.
     */
    public function getPrimaryTextColorAttribute(): string
    {
        return $this->calculateContrastTextColor($this->primary_color);
    }

    /**
     * High-contrast text color (#0f172a or #ffffff) for elements on accent brand background.
     */
    public function getAccentTextColorAttribute(): string
    {
        return $this->calculateContrastTextColor($this->accent_color);
    }

    /**
     * Calculate whether dark or light text is optimal based on hex luminance.
     */
    protected function calculateContrastTextColor(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#ffffff';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Relative luminance using ITU-R BT.601 coefficients
        $luminance = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $luminance > 165 ? '#0f172a' : '#ffffff';
    }

    /**
     * Maximum number of staff accounts allowed under showroom's subscription plan.
     */
    public function getMaxStaffAccounts(): int
    {
        if ($this->subscription) {
            $overrideLimit = $this->subscription->getLimit('max_staff_accounts');
            if ($overrideLimit !== null) {
                return $overrideLimit;
            }
        }

        return (int) ($this->subscription?->plan?->max_staff_accounts ?? $this->max_staff_accounts ?? 5);
    }

    /**
     * Check if showroom has reached its plan staff capacity limit.
     */
    public function hasReachedStaffLimit(): bool
    {
        $currentCount = $this->memberships()->count();

        return $currentCount >= $this->getMaxStaffAccounts();
    }
}
