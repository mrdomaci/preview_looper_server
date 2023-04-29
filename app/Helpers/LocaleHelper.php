<?php
declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class LocaleHelper
{
    private const SUPPORTED_LOCALES = [
        'cs',
        'sk',
        'hu',
        'en'
    ];
    public static function getLocale(): string
    {
        return App::getLocale();
    }

    public static function setLocale(string $locale): void
    {
        if (ArrayHelper::containsValue(self::SUPPORTED_LOCALES, $locale)) {
            App::setLocale($locale);
        } else {
            App::setLocale(self::SUPPORTED_LOCALES[0]);
        }
    }
}