<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        if (!ArrayHelper::containsValue(self::SUPPORTED_LOCALES, $locale)) {
            $locale = self::SUPPORTED_LOCALES[0];
        }
        App::setLocale($locale);
        Session::put('locale', $locale);
    }
}
