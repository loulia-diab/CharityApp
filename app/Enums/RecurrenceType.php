<?php

namespace App\Enums;

enum RecurrenceType: string

{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    public function label(string $locale = 'en'): string
    {
        return match ($this) {

            self::Daily => $locale === 'ar' ? 'يومي' : 'Daily',
            self::Weekly=> $locale === 'ar' ? 'أسبوعي' : 'Weekly',
            self::Monthly => $locale === 'ar' ? 'شهري' : 'Monthly',

        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
