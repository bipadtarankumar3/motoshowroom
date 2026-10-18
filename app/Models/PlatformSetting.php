<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    /**
     * Cache key prefix for platform settings.
     */
    public const CACHE_PREFIX = 'platform_setting:';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /**
     * Retrieve a setting value by key, with optional fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(self::CACHE_PREFIX.$key, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return $setting->value ?? $default;
        });
    }

    /**
     * Set or update a setting value by key and clear cache.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $description = null): self
    {
        $payload = [
            'value' => is_array($value) || is_object($value) ? json_encode($value) : (string) $value,
            'group' => $group,
        ];

        if ($description !== null) {
            $payload['description'] = $description;
        }

        $setting = static::updateOrCreate(['key' => $key], $payload);

        Cache::forget(self::CACHE_PREFIX.$key);

        return $setting;
    }

    /**
     * Clear all cached platform settings.
     */
    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX.$key);
        }
    }
}
