<?php
declare(strict_types=1);

namespace App\Helpers;

class LocaleHelper
{
    private const SUPPORTED_LOCALES = [
        'cs',
        'sk',
        'hu',
    ];
    public static function getLocale(): string
    {
        return app()->getLocale();
    }

    public static function setLocale(string $locale): void
    {
        if (ArrayHelper::containsValue(self::SUPPORTED_LOCALES, $locale)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(self::SUPPORTED_LOCALES[0]);
        }
    }
}