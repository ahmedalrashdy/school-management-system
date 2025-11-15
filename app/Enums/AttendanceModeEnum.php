<?php

namespace App\Enums;

enum AttendanceModeEnum: int
{
    case Daily = 1;      // تحضير مرة واحدة في اليوم
    case SplitDaily = 2; // تحضير مرتين (فترتين: قبل/بعد الفسحة)
    case PerPeriod = 3;  // تحضير لكل حصة دراسية

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'تحضير يومي',
            self::SplitDaily => 'تحضير على فترتين',
            self::PerPeriod => 'تحضير لكل حصة',
        };
    }

    public static function options(): array
    {
        return [
            self::Daily->value => self::Daily->label(),
            self::SplitDaily->value => self::SplitDaily->label(),
            self::PerPeriod->value => self::PerPeriod->label(),
        ];
    }
}
