<?php

namespace App\Enums;

enum ActivityLogNameEnum: string
{
    case Academics = 'academics';
    case Administration = 'administration';
    case Finance = 'finance';
    case System = 'system';
    case Relations = 'relations';
    case Content = 'content';
    case Default = 'default';

    public function label(): string
    {
        return match ($this) {
            self::Academics => 'أكاديمي',
            self::Administration => 'إداري',
            self::Finance => 'مالي',
            self::System => 'النظام',
            self::Relations => 'علاقات',
            self::Content => 'محتوى',
            self::Default => 'عام',
        };
    }
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn($carry, $case) => $carry + [$case->value => $case->label()],
            []
        );
    }
}
