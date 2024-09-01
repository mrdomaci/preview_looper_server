<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use App\Helpers\DateTimeHelper;
use DateTime;

class OrderFilter
{
    private const FILTERS = [
        'include' => 'string',
        'statusId' => 'string',
        'shippingGuid' => 'string',
        'shippingCompanyCode' => 'string',
        'paymentMethodGuid' => 'string',
        'creationTimeFrom' => DateTime::class,
        'creationTimeTo' => DateTime::class,
        'codeFrom' => 'string',
        'codeTo' => 'string',
        'customerGuid' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'productCode' => 'string',
        'changeTimeFrom' => DateTime::class,
        'changeTimeTo' => DateTime::class,
        'sourceId' => 'string',
    ];
    
    public function __construct(
        private string $key,
        private string|DateTime $value,
    ) {
        $keyCheck = false;
        foreach (self::FILTERS as $filterKey => $filterValue) {
            if (self::FILTERS[$filterKey] === DateTime::class) {
                if ($this->value instanceof DateTime) {
                    $keyCheck = true;
                }
            } else if (self::FILTERS[$filterKey] === 'string') {
                if (is_string($filterValue)) {
                    $keyCheck = true;
                }
            }
        }
        if ($keyCheck === false) {
            throw new \Exception('Invalid filter key' . $this->key . ' and value ' . $this->value);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        if ($this->value instanceof DateTime) {
            $result = DateTimeHelper::getDateTimeString($this->value);
        } else {
            $result = $this->value;
        }
        return $result;
    }
}
