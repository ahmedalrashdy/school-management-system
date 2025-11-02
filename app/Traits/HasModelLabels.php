<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait HasModelLabels
{
    protected static function modelKey(): string
    {
        return Str::snake(class_basename(static::class));
    }

    public static function label(): string
    {
        return __('models.'.static::modelKey().'.singular');
    }

    public static function labelPlural(): string
    {
        return __('models.'.static::modelKey().'.plural');
    }

    public static function labelCount(int $count): string
    {
        return Lang::choice(
            'models.'.static::modelKey().'.count',
            $count,
            ['count' => $count]
        );
    }
}
