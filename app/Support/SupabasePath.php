<?php

namespace App\Support;

use Illuminate\Support\Str;

class SupabasePath
{
    public static function normalize(string $path): string
    {
        $path = ltrim($path, '/');

        // buang prefix submissions/ kalau ada di DB
        if (Str::startsWith($path, 'submissions/')) {
            $path = Str::after($path, 'submissions/');
        }

        return $path;
    }
}