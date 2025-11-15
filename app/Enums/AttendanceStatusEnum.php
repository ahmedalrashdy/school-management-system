<?php

namespace App\Enums;

enum AttendanceStatusEnum: int
{
    case Present = 1;
    case Absent = 2;
    case Late = 3;
    case Excused = 4;

    public function label(): string
    {
        return match ($this) {
            self::Present => 'حاضر',
            self::Absent => 'غائب',
            self::Late => 'متأخر',
            self::Excused => 'معذور',
        };
    }

    public static function options(): array
    {
        return [
            self::Present->value => self::Present->label(),
            self::Absent->value => self::Absent->label(),
            self::Late->value => self::Late->label(),
            self::Excused->value => self::Excused->label(),
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::Present => 'success',
            self::Absent => 'danger',
            self::Late => 'warning',
            self::Excused => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Present => 'fas fa-check-circle',
            self::Absent => 'fas fa-times-circle',
            self::Late => 'fas fa-clock',
            self::Excused => 'fas fa-user-check',
        };
    }
}
