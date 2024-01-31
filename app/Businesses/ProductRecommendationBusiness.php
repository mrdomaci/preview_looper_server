<?php
declare(strict_types=1);

namespace App\Businesses;

use App\Models\Product;

class ProductRecommendationBusiness {

    private const PRODUCT_HTML_TEMPLATE = '<tr class="removeable" data-micro="cartItem" data-micro-identifier="%s" data-micro-sku="0011" data-testid="productItem_%s"><td class="cart-p-image"><a target="blank" href="%s?utm_source=upsale;utm_medium=cart"><img src="%s" data-src="%s" alt="%s"></a></td><td class="p-name" data-testid="cartProductName"><a target="blank" href="%s?utm_source=upsale;utm_medium=cart" class="main-link" data-testid="cartWidgetProductName">%s</a></td><td class="p-availability p-cell"><span class="p-label">Dostupnost</span><strong class="availability-label" style="color: #009901">Skladem</strong></td><td class="p-total"><span class="p-label">Součet</span><strong class="price-final" data-testid="cartPrice">%s</strong></td></tr>';

    public function getResponseForProduct(Product $product): string {
        return self::PRODUCT_HTML_TEMPLATE;
    }
}