<?php

declare(strict_types=1);

namespace App\Connector;

class EshopResponse
{
    public function __construct(
        private ?string $name,
        private ?string $title,
        private ?string $category,
        private ?string $subtitle,
        private ?string $url,
        private ?string $contactPerson,
        private ?string $email,
        private ?string $phone,
        private ?string $street,
        private ?string $city,
        private ?string $zip,
        private ?string $country,
        private ?string $vatNumber,
        private ?string $oauthUrl,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getOauthUrl(): ?string
    {
        return $this->oauthUrl;
    }
}
