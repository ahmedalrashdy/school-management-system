<?php

namespace App\Enums;

enum DayPartEnum: int
{
    case FULL_DAY = 1;        // اليوم كامل
    case PART_ONE_ONLY = 2;   // الجزء الأول فقط - قبل الفسحة
    case PART_TWO_ONLY = 3;   // الجزء الثاني فقط - بعد الفسحة

    public function label(): string
    {
        return match ($this) {
            self::FULL_DAY => 'اليوم كامل',
            self::PART_ONE_ONLY => 'الفترة الأولى',
            self::PART_TWO_ONLY => 'الفترة الثانية',
        };
    }

    public static function options(): array
    {
        return [
            self::FULL_DAY->value => self::FULL_DAY->label(),
            self::PART_ONE_ONLY->value => self::PART_ONE_ONLY->label(),
            self::PART_TWO_ONLY->value => self::PART_TWO_ONLY->label(),
        ];
    }
}
