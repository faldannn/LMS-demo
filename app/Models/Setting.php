<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected static ?array $runtimeCache = null;

    public static function allCached(): array
    {
        if (static::$runtimeCache !== null) {
            return static::$runtimeCache;
        }

        try {
            static::$runtimeCache = Cache::remember('app.settings', 86400, function () {
                return static::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            static::$runtimeCache = [];
        }

        return static::$runtimeCache ?? [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::allCached();

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        static::$runtimeCache = null;
        Cache::forget('app.settings');
    }

    public static function getGroup(string $group): array
    {
        try {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function isFeatureEnabled(string $featureKey, bool $default = false): bool
    {
        $val = static::get("feature_{$featureKey}", null);
        if ($val === null) {
            $val = static::get($featureKey, null);
        }
        if ($val === null) {
            return $default;
        }

        if ($val === '0' || $val === 0 || $val === 'false' || $val === false || $val === 'off' || $val === '') {
            return false;
        }

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }
}
