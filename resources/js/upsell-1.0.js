if (isCart() == true) {
    const us_cart = document.querySelectorAll('.cart-table');
    if (us_cart.length > 0) {
        us_cart.forEach(async function (el) { // Use async function to use await inside forEach
            const us_image_cdn = getImageCdn();
            const us_response = getRecommnededProducts();
            let us_result = '<hr><table class="cart-table"><tbody>';
            const result = await us_response; // Wait for response to resolve
            await Promise.all(result.map(async function(item) { // Use Promise.all() to wait for all us_link_response.then() promises
                const us_link_response = await goToURL(item.url);
                const response = await us_link_response.text();
                const tempContainer = document.createElement('div');
                tempContainer.innerHTML = response;
                const availabilitySpan = tempContainer.querySelector('span.availability-label');
                let us_availabilty = '-';
                if (availabilitySpan) {
                    us_availabilty = availabilitySpan.textContent.trim();
                }
                let us_add_to_cart = '-';
                const addToCartButton = tempContainer.querySelector('button[data-testid="buttonAddToCart"]');
                if (addToCartButton) {
                    us_add_to_cart = addToCartButton.outerHTML;
                }
                let us_product_id = '-';
                const product_id = tempContainer.querySelector('input[name="productId"]');
                if (product_id) {
                    us_product_id = product_id.getAttribute('value');
                }
                let us_language = getShoptetDataLayer('language');
                let us_call_to_action = shoptet.messages['toCart'];

                let us_result_item = `
                    <tr class="removeable" data-micro="cartItem" data-micro-identifier="${item.guid}" data-micro-sku="${item.code}" data-testid="productItem_${item.guid}">
                        <td class="cart-p-image"><a href="${item.url}"><img src="${us_image_cdn}${item.image_url}" data-src="${us_image_cdn}${item.image_url}" alt="${item.name}"></a></td>
                        <td class="p-name" data-testid="cartProductName"><a href="${item.url}" class="main-link" data-testid="cartWidgetProductName">${item.name}</a></td>
                        <td class="p-availability p-cell">${us_availabilty}</td>
                        <td class="p-quantity p-cell">
                            <form action="/action/Cart/addCartItem/" method="post" id="product-detail-form">
                                <meta itemprop="productID" content="${us_product_id}">
                                <meta itemprop="identifier" content="${item.guid}">
                                <meta itemprop="sku" content="${item.code}">
                                <input type="hidden" name="productId" value="${us_product_id}">
                                <input type="hidden" name="priceId" value="${us_product_id}">
                                <div class="add-to-cart">
                                    <form action="/action/Cart/addCartItem/" method="post" class="pr-action">
                                        <input type="hidden" name="language" value="${us_language}">
                                        <input type="hidden" name="priceId" value="${item.id}">
                                        <input type="hidden" name="productId" value="${item.id}">
                                        <input type="hidden" name="amount" value="1" autocomplete="off">
                                        <button type="submit" class="btn btn-cart add-to-cart-button" data-testid="buttonAddToCart">
                                            <span>${us_call_to_action}</span>
                                        </button>
                                    </form>
                                </div>
                            </form>
                        </td>
                        <td class="p-total"><strong class="price-final" data-testid="cartItemPrice">${item.price}</strong><span class="unit-value">/ ${item.unit}</span></td>
                    </tr>`;
                us_result += us_result_item;
            }));
            us_result += '</tbody></table>';
            el.insertAdjacentHTML('afterend', us_result);
        });
    }
}

async function getRecommnededProducts() {
    if (shouldCallServer() === false) {
        return null;
    }
    const us_project_id = getShoptetDataLayer('projectId');
    const us_project_id_modulo = us_project_id % 11;
    const us_cart_items = getCartItemsGUIDS();
    try {
        const response = await fetch('https://slabihoud.cz/products/' + us_project_id + '/' + us_project_id_modulo + '/' + us_cart_items.toString());
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
}

async function goToURL(url) {
    try {
        const response = await fetch(url);
        return response;
    }
    catch (error) {
        console.error('Error:', error);
    }
}

function shouldCallServer() {
    return true;
}

function getCartItemsGUIDS() {
    let us_result_items = [];
    const us_cart_items = getCartItems();
    us_cart_items.forEach(function (el) {
        us_result_items.push(el.getAttribute('data-micro-identifier'));
    });
    return us_result_items;
}

function getCartItems() {
    return document.querySelectorAll('[data-micro-identifier]');
}

function getImageCdn() {
    const us_cart_items = document.querySelectorAll('td.cart-p-image a img');
    if (us_cart_items.length > 0) {
        let us_image_src = us_cart_items[0].getAttribute('data-src');
        const us_image_parts = us_image_src.split('/');
        us_image_parts.pop();
        const us_image_cdn = us_image_parts.join('/');
        return us_image_cdn + '/';
    }
}

function isCart() {
    if (getShoptetDataLayer('pageType') == 'cart') {
        return true;
    }
    return false;
}

