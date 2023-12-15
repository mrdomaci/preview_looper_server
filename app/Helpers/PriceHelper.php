<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\ProductVariantResponse;

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

        if ($lowestPrice === $highestPrice) {
            return $lowestPrice . ' ' . $currency;
        }

        return $lowestPrice . ' - ' . $highestPrice . ' ' . $currency;
    }
}