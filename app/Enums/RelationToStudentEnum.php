<?php

namespace App\Enums;

enum RelationToStudentEnum: int
{
    case Father = 1;
    case Mother = 2;
    case Brother = 3;
    case Sister = 4;
    case Grandfather = 5;
    case Grandmother = 6;
    case Uncle = 7;       // العم
    case Aunt = 8;        // العمة
    case MaternalUncle = 9;  // الخال
    case MaternalAunt = 10;  // الخالة
    case Guardian = 11;      // ولي أمر غير قريب مباشر

    public function label(): string
    {
        return match ($this) {
            self::Father => 'الأب',
            self::Mother => 'الأم',
            self::Brother => 'الأخ',
            self::Sister => 'الأخت',
            self::Grandfather => 'الجد',
            self::Grandmother => 'الجدة',
            self::Uncle => 'العم',
            self::Aunt => 'العمة',
            self::MaternalUncle => 'الخال',
            self::MaternalAunt => 'الخالة',
            self::Guardian => 'ولي أمر',
        };
    }

    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn ($carry, $case) => $carry + [$case->value => $case->label()],
            []
        );
    }
}
