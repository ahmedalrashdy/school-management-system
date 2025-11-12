<?php

namespace App\Enums;

enum DayOfWeekEnum: int
{
    case Saturday = 1;
    case Sunday = 2;
    case Monday = 3;
    case Tuesday = 4;
    case Wednesday = 5;
    case Thursday = 6;
    case Friday = 7;

    public function label(): string
    {
        return match ($this) {
            self::Saturday => 'السبت',
            self::Sunday => 'الأحد',
            self::Monday => 'الاثنين',
            self::Tuesday => 'الثلاثاء',
            self::Wednesday => 'الأربعاء',
            self::Thursday => 'الخميس',
            self::Friday => 'الجمعة',
        };
    }

    public static function options(): array
    {
        return [
            self::Saturday->value => self::Saturday->label(),
            self::Sunday->value => self::Sunday->label(),
            self::Monday->value => self::Monday->label(),
            self::Tuesday->value => self::Tuesday->label(),
            self::Wednesday->value => self::Wednesday->label(),
            self::Thursday->value => self::Thursday->label(),
            self::Friday->value => self::Friday->label(),
        ];
    }

    /**
     * Get the lowercase key name for the day (e.g., 'saturday', 'sunday').
     */
    public function key(): string
    {
        return match ($this) {
            self::Saturday => 'saturday',
            self::Sunday => 'sunday',
            self::Monday => 'monday',
            self::Tuesday => 'tuesday',
            self::Wednesday => 'wednesday',
            self::Thursday => 'thursday',
            self::Friday => 'friday',
        };
    }

    /**
     * Get all days with their keys and labels for use in forms.
     */
    public static function formOptions(): array
    {
        return array_map(
            fn ($case) => [
                'key' => $case->key(),
                'label' => $case->label(),
                'value' => $case->value,
            ],
            self::cases()
        );
    }

    /**
     * Get enum case from name (case-insensitive)
     */
    public static function fromName(string $name): ?self
    {
        $name = strtolower($name);

        foreach (self::cases() as $case) {
            if (strtolower($case->name) === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Convert Carbon's dayOfWeek (0=Sunday, 6=Saturday) to DayOfWeekEnum
     */
    public static function fromCarbonDayOfWeek(int $carbonDay): self
    {
        return match ($carbonDay) {
            0 => self::Sunday,
            1 => self::Monday,
            2 => self::Tuesday,
            3 => self::Wednesday,
            4 => self::Thursday,
            5 => self::Friday,
            6 => self::Saturday,
        };
    }
}
