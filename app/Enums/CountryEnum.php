<?php

declare(strict_types=1);

namespace App\Enums;

enum CountryEnum: string
{
    case CZECH = 'CZ';
    case HUNGARY = 'HU';

    public static function getByValue(?string $value = 'CZ'): self
    {
        $value = strtoupper($value);
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return self::CZECH;
    }

    public function isCzech(): bool
    {
        return $this->value === self::CZECH->value;
    }

    public function isHungary(): bool
    {
        return $this->value === self::HUNGARY->value;
    }

    public function getShoptetClientId(): string
    {
        return env('SHOPTET_CLIENT_ID_' . $this->value);
    }

    public function getShoptetClientSecret(): string
    {
        return env('SHOPTET_CLIENT_SECRET_' . $this->value);
    }

    public function getShoptetOauthServerTokenUrl(): string
    {
        return env('SHOPTET_OAUTH_SERVER_TOKEN_URL_' . $this->value);
    }

    public function getApiAccessTokenUrl(): string
    {
        return env('SHOPTET_API_ACCESS_TOKEN_URL_' . $this->value);
    }
}
