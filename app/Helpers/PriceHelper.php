<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Connector\Shoptet\ProductVariantResponse;
use App\Models\Currency;

class PriceHelper
{
    /**
     * @param array<ProductVariantResponse> $productVariants
     */
    public static function getUnfiedPriceString(array $productVariants): ?string
    {
        $lowestPrice = null;
        $highestPrice = null;
        $currency = null;
        /** @var ProductVariantResponse $productVariant */
        foreach ($productVariants as $productVariant) {
            if ($lowestPrice === null) {
                $lowestPrice = $productVariant->getPrice();
            }
            if ($highestPrice === null) {
                $highestPrice = $productVariant->getPrice();
            }
            if ($currency === null) {
                $currency = $productVariant->getCurrencyCode();
            }
            if ($productVariant->getPrice() < $lowestPrice) {
                $lowestPrice = $productVariant->getPrice();
            }
            if ($productVariant->getPrice() > $highestPrice) {
                $highestPrice = $productVariant->getPrice();
            }
        }
        if ($lowestPrice === null || $highestPrice === null || $currency === null) {
            return null;
        }

        $price = (string) $lowestPrice;

        if ($lowestPrice !== $highestPrice) {
            $price = $lowestPrice . ' - ' . $highestPrice;
        }

        return Currency::formatPrice($price, $currency);
    }
}
