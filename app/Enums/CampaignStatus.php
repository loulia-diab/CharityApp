<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Pending = 'pending';

    case Active = 'active';

    case Complete = 'complete';
    case Archived = 'archived';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Pending => $locale === 'ar' ? 'معلقة' : 'Pending',
            self::Active => $locale === 'ar' ? 'فعالة' : 'Active',
            self::Complete => $locale === 'ar' ? 'مكتملة' : 'Complete',
            self::Archived => $locale === 'ar' ? 'مؤرشفة' : 'Archived',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
