<?php

namespace App\Enums;

enum RecurrenceType: string

{
    case Once = 'once';
    case Daily = 'daily';

    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Once =>$locale=='ar' ? 'مرة واحدة' : 'Once',
            self::Daily => $locale === 'ar' ? 'يومي' : 'Daily',
            self::Monthly => $locale === 'ar' ? 'شهري' : 'Monthly',
            self::Yearly => $locale === 'ar' ? 'سنوي' : 'Yearly',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
