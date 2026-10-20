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
        'avatar_type',
        'avatar_custom_url',
        'voice_enabled',
        'voice_language',
        'voice_gender',
        'voice_pitch',
        'voice_name',
        'voice_welcome_text',
        'voice_audio_path',
        'welcome_video_path',
        'welcome_video_type',
        'gemini_api_key',
        'presenter_mode',
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
            'voice_enabled' => 'boolean',
            'voice_pitch' => 'float',
            'suggested_prompts' => 'array',
            'custom_faqs' => 'array',
        ];
    }

    /**
     * Get effective voice gender based on avatar or explicit choice.
     */
    public function getEffectiveVoiceGenderAttribute(): string
    {
        if (! empty($this->voice_gender)) {
            return $this->voice_gender;
        }

        return $this->avatar_type === 'female_saree' ? 'female' : 'male';
    }

    /**
     * Get effective speech pitch.
     */
    public function getEffectiveVoicePitchAttribute(): float
    {
        if (! empty($this->voice_pitch) && $this->voice_pitch > 0) {
            return (float) $this->voice_pitch;
        }

        return $this->effective_voice_gender === 'female' ? 1.22 : 0.88;
    }

    /**
     * Get the avatar image URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_type === 'female_saree') {
            return asset('images/ai-avatars/avatar-female-saree-cutout.png');
        }

        if ($this->avatar_type === 'custom' && ! empty($this->avatar_custom_url)) {
            if (str_starts_with($this->avatar_custom_url, 'http://') || str_starts_with($this->avatar_custom_url, 'https://')) {
                return $this->avatar_custom_url;
            }

            return asset('storage/'.$this->avatar_custom_url);
        }

        return asset('images/ai-avatars/avatar-male-tvs-cutout.png');
    }

    /**
     * Get the default voice greeting text.
     */
    public function getVoiceWelcomeTextOrDefaultAttribute(): string
    {
        if (! empty($this->voice_welcome_text)) {
            return $this->voice_welcome_text;
        }

        $showroomName = $this->showroom?->name ?? 'Sawaranti TVS';

        if ($this->voice_language === 'hi-IN') {
            return "नमस्ते! {$showroomName} में आपका स्वागत है। मैं आपका AI सलाहकार हूँ। बताइए आज मैं आपकी क्या सहायता कर सकता हूँ?";
        }

        return "Namaste! Welcome to {$showroomName}. I am your AI assistant. How may I help you today?";
    }

    /**
     * Get pre-recorded voice audio URL if uploaded.
     */
    public function getVoiceAudioUrlAttribute(): ?string
    {
        if (empty($this->voice_audio_path)) {
            return null;
        }

        if (str_starts_with($this->voice_audio_path, 'http://') || str_starts_with($this->voice_audio_path, 'https://')) {
            return $this->voice_audio_path;
        }

        return asset('storage/'.$this->voice_audio_path);
    }

    /**
     * Get photorealistic welcome video URL if uploaded or linked.
     */
    public function getWelcomeVideoUrlAttribute(): ?string
    {
        if (empty($this->welcome_video_path)) {
            return null;
        }

        if (str_starts_with($this->welcome_video_path, 'http://') || str_starts_with($this->welcome_video_path, 'https://')) {
            return $this->welcome_video_path;
        }

        return asset('storage/'.$this->welcome_video_path);
    }

    /**
     * Check if a welcome video is configured.
     */
    public function getHasWelcomeVideoAttribute(): bool
    {
        return ! empty($this->welcome_video_path);
    }

    /**
     * Get effective Gemini API key for this showroom.
     */
    public function getEffectiveGeminiApiKeyAttribute(): ?string
    {
        return $this->gemini_api_key ?: config('showroom.ai_assistant.api_key') ?: env('GEMINI_API_KEY');
    }

    /**
     * Get effective presenter mode ('avatar' or 'video').
     */
    public function getEffectivePresenterModeAttribute(): string
    {
        if ($this->presenter_mode === 'video' && $this->has_welcome_video) {
            return 'video';
        }

        return 'avatar';
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
