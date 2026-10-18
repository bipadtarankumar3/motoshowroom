<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAssistantSetting extends Model
{
    use BelongsToTenant, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'bot_name',
        'welcome_message',
        'tone',
        'is_enabled',
        'suggested_prompts',
        'custom_faqs',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'suggested_prompts' => 'array',
            'custom_faqs' => 'array',
        ];
    }

    /**
     * Get normalized custom FAQs list.
     *
     * @return list<array<string, string>>
     */
    public function getFaqsListAttribute(): array
    {
        return is_array($this->custom_faqs) ? array_values($this->custom_faqs) : [];
    }

    /**
     * Get default welcome message tailored for this showroom if unset.
     */
    public function getWelcomeMessageOrDefaultAttribute(): string
    {
        if (! empty($this->welcome_message)) {
            return $this->welcome_message;
        }

        $showroomName = $this->showroom?->name ?? 'our showroom';

        return "Hello! I am your AI concierge at {$showroomName}. How can I assist you today? Ask me about motorcycle recommendations, test rides, service bookings, or current offers!";
    }

    /**
     * The showroom tenant this setting belongs to.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }
}
