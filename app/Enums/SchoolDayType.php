<?php

namespace App\Enums;

enum SchoolDayType: int
{
    case SchoolDay = 1; // يوم دراسي
    case Holiday = 2; // عطلة كاملة
    case PartialHoliday = 3; // عطلة جزئية

    public function label(): string
    {
        return match ($this) {
            self::SchoolDay => 'يوم دراسي',
            self::Holiday => 'عطلة',
            self::PartialHoliday => 'عطلة جزئية',
        };
    }

    public static function options(): array
    {
        return [
            self::SchoolDay->value => self::SchoolDay->label(),
            self::Holiday->value => self::Holiday->label(),
            self::PartialHoliday->value => self::PartialHoliday->label(),
        ];
    }
}
