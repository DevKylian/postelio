<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        $value = $setting->value;

        // Try to cast back to boolean if it looks like one
        if ($value === '1' || $value === 'true' || $value === true) return true;
        if ($value === '0' || $value === 'false' || $value === false) return false;

        return $value;
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
