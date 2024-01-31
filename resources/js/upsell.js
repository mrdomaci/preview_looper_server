const cart = document.querySelectorAll('.cart-table');
if (cart.length > 0) {
    cart.forEach(function (el) {
        const htmlToInsert = 
        '<hr><table class="cart-table"><tbody><tr class="removeable" data-micro="cartItem" data-micro-identifier="d7282b43-d957-11e0-b04f-57a43310b768" data-micro-sku="0011" data-testid="productItem_d7282b43-d957-11e0-b04f-57a43310b768"><td class="cart-p-image"><a target="blank" href="/ortopedicke-vlozky/?utm_source=upsale;utm_medium=cart"><img src="https://cdn.myshoptet.com/usr/566732.myshoptet.com/user/shop/related/66_screenshot-2024-01-13-at-11-31-38.png?65a266b1" data-src="https://cdn.myshoptet.com/usr/566732.myshoptet.com/user/shop/related/66_screenshot-2024-01-13-at-11-31-38.png?65a266b1" alt="Screenshot 2024 01 13 at 11.31.38"></a></td><td class="p-name" data-testid="cartProductName"><a target="blank" href="/ortopedicke-vlozky/?utm_source=upsale;utm_medium=cart" class="main-link" data-testid="cartWidgetProductName">Ortopedické vložky</a></td><td class="p-availability p-cell"><span class="p-label">Dostupnost</span><strong class="availability-label" style="color: #009901">Skladem</strong></td><td class="p-total"><span class="p-label">Součet</span><strong class="price-final" data-testid="cartPrice">250 Kč</strong></td></tr></tbody></table>';
        el.insertAdjacentHTML('afterend', htmlToInsert);
    });
}

