<?php

namespace App\Enums;

enum GenderEnum: int
{
    case Male = 1;
    case Female = 2;

    public function label(): string
    {
        return match ($this) {
            self::Male => 'ذكر',
            self::Female => 'أنثى',
        };
    }

    public static function options(): array
    {
        return [
            self::Male->value => self::Male->label(),
            self::Female->value => self::Female->label(),
        ];
    }
}
