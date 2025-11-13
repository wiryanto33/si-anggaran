<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    protected $table = 'settings';

    public static function get(string $key, $default = null)
    {
        return cache()->remember("setting.$key", 3600, function () use ($key, $default) {
            $val = static::where('key', $key)->value('value');
            return $val !== null ? $val : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting.$key");
    }
}

