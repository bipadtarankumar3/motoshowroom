<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiConversation extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'session_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'detected_intent',
        'lead_converted',
        'enquiry_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lead_converted' => 'boolean',
        ];
    }

    /**
     * All chat messages within this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id')->oldest();
    }

    /**
     * The authenticated user if logged in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The converted enquiry if lead was captured.
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class, 'enquiry_id');
    }

    /**
     * The showroom tenant this conversation belongs to.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }
}
