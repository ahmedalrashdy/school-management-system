<?php

namespace App\Enums;

enum AcademicYearStatus: int
{
    case Active = 1;
    case Upcoming = 2;
    case Archived = 3;

    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشطة',
            self::Upcoming => 'قادمة',
            self::Archived => 'مؤرشفة',
        };
    }

    public static function options(): array
    {
        return [
            self::Active->value => self::Active->label(),
            self::Upcoming->value => self::Upcoming->label(),
            self::Archived->value => self::Archived->label(),
        ];
    }
}
