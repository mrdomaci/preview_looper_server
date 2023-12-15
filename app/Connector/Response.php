<?php
declare(strict_types=1);

namespace App\Connector;

use App\Exceptions\ApiResponsePaginatorFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use DateTime;
use Exception;
use Nette\Utils\Strings;

class Response
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    public function __construct(
        private array $data,
        private ?array $errors = [],
    ) {
        if ($errors !== null) {
            foreach($errors as $key => $error) {
                LoggerHelper::log($key);
                LoggerHelper::log($error);
            }
        }
    }

    public function getPaginator(): Paginator
    {
        if (ArrayHelper::containsKey($this->data, 'paginator') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator not found in response'));
        }
        $paginator = $this->data['paginator'];
        if (ArrayHelper::containsKey($paginator, 'totalCount') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator total count not found in response'));
        }
        if (ArrayHelper::containsKey($paginator, 'page') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator page not found in response'));
        }
        if (ArrayHelper::containsKey($paginator, 'pageCount') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator page count not found in response'));
        }
        if (ArrayHelper::containsKey($paginator, 'itemsOnPage') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator items on page not found in response'));
        }
        if (ArrayHelper::containsKey($paginator, 'itemsPerPage') === false) {
            throw new ApiResponsePaginatorFailException(new Exception('Paginator items per page not found in response'));
        }
        return new Paginator(
            $paginator['totalCount'],
            $paginator['page'],
            $paginator['pageCount'],
            $paginator['itemsOnPage'],
            $paginator['itemsPerPage'],
        );
    }

    public function getProducts(): ?ProductListResponse
    {
        if (ArrayHelper::containsKey($this->data, 'products') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data, 'paginator') === false) {
            return null;
        }

        if (ArrayHelper::containsKey($this->data['paginator'], 'totalCount') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['paginator'], 'page') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['paginator'], 'pageCount') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['paginator'], 'itemsOnPage') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['paginator'], 'itemsPerPage') === false) {
            return null;
        }
        $productListResponse = new ProductListResponse(
            $this->data['paginator']['totalCount'],
            $this->data['paginator']['page'],
            $this->data['paginator']['pageCount'],
            $this->data['paginator']['itemsOnPage'],
            $this->data['paginator']['itemsPerPage'],
            []
        );
        foreach ($this->data['products'] as $product) {
            $creationTime = null;
            $changeTime = null;
            $name = null;
            $voteAverageScore = null;
            $voteCount = null;
            $type = null;
            $visibility = null;
            $defaultCategory = null;
            $url = null;
            $supplier = null;
            $brand = null;

            if (ArrayHelper::containsKey($product, 'guid') === false) {
                continue;
            } else {
                $guid = $product['guid'];
            }
            if (ArrayHelper::containsKey($product, 'creationTime') && $product['creationTime'] !== null) {
                $creationTime = new DateTime($product['creationTime']);
            }
            if (ArrayHelper::containsKey($product, 'changeTime') && $product['changeTime'] !== null) {
                $changeTime = new DateTime($product['changeTime']);
            }
            if (ArrayHelper::containsKey($product, 'name')) {
                $name = $product['name'];
            }
            if (ArrayHelper::containsKey($product, 'voteAverageScore') && $product['voteAverageScore'] !== null) {
                $voteAverageScore = (float) $product['voteAverageScore'];
            }
            if (ArrayHelper::containsKey($product, 'voteCount')) {
                $voteCount = $product['voteCount'];
            }
            if (ArrayHelper::containsKey($product, 'type')) {
                $type = $product['type'];
            }
            if (ArrayHelper::containsKey($product, 'visibility')) {
                $visibility = $product['visibility'];
            }
            if (ArrayHelper::containsKey($product, 'defaultCategory')) {
                if (ArrayHelper::isArray($product['defaultCategory']) 
                    && ArrayHelper::containsKey($product['defaultCategory'], 'guid')
                    && $product['defaultCategory']['guid'] !== null
                    && ArrayHelper::containsKey($product['defaultCategory'], 'name')
                    && $product['defaultCategory']['name'] !== null
                ) {
                    $defaultCategory = new ProductCategory(
                        $product['defaultCategory']['guid'],
                        $product['defaultCategory']['name'],
                    );
                }
            }
            if (ArrayHelper::containsKey($product, 'url')) {
                $url = $product['url'];
            }
            if (ArrayHelper::containsKey($product, 'supplier')) {
                if (ArrayHelper::isArray($product['supplier'])) {
                    $product['supplier'] = implode(', ', $product['supplier']);
                }
                $supplier = $product['supplier'];
            }
            if (ArrayHelper::containsKey($product, 'brand')) {
                if (ArrayHelper::isArray($product['brand']) &&  ArrayHelper::containsKey($product['brand'], 'guid') && ArrayHelper::containsKey($product['brand'], 'name')) {
                    $brand = new ProductBrand(
                        $product['brand']['guid'],
                        $product['brand']['name'],
                    );
                }
            }
            $productListResponse->addProduct(new ProductResponse($guid, $creationTime, $changeTime, $name, $voteAverageScore, $voteCount, $type, $visibility, $defaultCategory, $url, $supplier, $brand));
        }
        return $productListResponse;
    }

    public function getProductDetails(): ?ProductDetailResponse
    {
        $creationTime = null;
        $changeTime = null;
        $name = null;
        $voteAverageScore = null;
        $voteCount = null;
        $type = null;
        $visibility = null;
        $defaultCategory = null;
        $url = null;
        $supplier = null;
        $brand = null;
        $perex = null;

        if (ArrayHelper::containsKey($this->data, 'guid') === false) {
            return null;
        } else {
            $guid = $this->data['guid'];
        }
        if (ArrayHelper::containsKey($this->data, 'creationTime') && $this->data['creationTime'] !== null) {
            $creationTime = new DateTime($this->data['creationTime']);
        }
        if (ArrayHelper::containsKey($this->data, 'changeTime') && $this->data['changeTime'] !== null) {
            $changeTime = new DateTime($this->data['changeTime']);
        }
        if (ArrayHelper::containsKey($this->data, 'name')) {
            $name = $this->data['name'];
        }
        if (ArrayHelper::containsKey($this->data, 'voteAverageScore') && $this->data['voteAverageScore'] !== null) {
            $voteAverageScore = (float) $this->data['voteAverageScore'];
        }
        if (ArrayHelper::containsKey($this->data, 'voteCount')) {
            $voteCount = $this->data['voteCount'];
        }
        if (ArrayHelper::containsKey($this->data, 'type')) {
            $type = $this->data['type'];
        }
        if (ArrayHelper::containsKey($this->data, 'visibility')) {
            $visibility = $this->data['visibility'];
        }
        if (ArrayHelper::containsKey($this->data, 'defaultCategory')) {
            if (ArrayHelper::isArray($this->data['defaultCategory']) 
                && ArrayHelper::containsKey($this->data['defaultCategory'], 'guid')
                && $this->data['defaultCategory']['guid'] !== null
                && ArrayHelper::containsKey($this->data['defaultCategory'], 'name')
                && $this->data['defaultCategory']['name'] !== null
            ) {
                $defaultCategory = new ProductCategory(
                    $this->data['defaultCategory']['guid'],
                    $this->data['defaultCategory']['name'],
                );
            }
        }
        if (ArrayHelper::containsKey($this->data, 'url')) {
            $url = $this->data['url'];
        }
        if (ArrayHelper::containsKey($this->data, 'supplier')) {
            if (ArrayHelper::isArray($this->data['supplier'])) {
                $this->data['supplier'] = implode(', ', $this->data['supplier']);
            }
            $supplier = $this->data['supplier'];
        }
        if (ArrayHelper::containsKey($this->data, 'brand')) {
            if (ArrayHelper::isArray($this->data['brand']) &&  ArrayHelper::containsKey($this->data['brand'], 'guid') && ArrayHelper::containsKey($this->data['brand'], 'name')) {
                $brand = new ProductBrand(
                    $this->data['brand']['guid'],
                    $this->data['brand']['name'],
                );
            }
        }

        if (ArrayHelper::containsKey($this->data, 'shortDescription')) {
            $perex = $this->data['shortDescription'];
        }

        if (ArrayHelper::containsKey($this->data, 'images') === false) {
            return null;
        }

        if (ArrayHelper::containsKey($this->data, 'variants') === false) {
            return null;
        }

        $productDetailResponse = new ProductDetailResponse($guid, $creationTime, $changeTime, $name, $voteAverageScore, $voteCount, $type, $visibility, $defaultCategory, $url, $supplier, $brand, $perex);

        foreach ($this->data['images'] as $image) {
            $productImageResponse = new ProductImageResponse(
                $image['name'],
                $image['priority'] ? (int) $image['priority'] : null,
                $image['seoName'],
                $image['cdnName'],
                $image['description'],
                new DateTime($image['changeTime']),
            );
            $productDetailResponse->addImage($productImageResponse);
        }

        foreach ($this->data['variants'] as $variant) {
            $availaility = null;
            if ($variant['availability'] !== null) {
                $availaility = $variant['availability']['name'];
            }
            $productVariantResponse = new ProductVariantResponse(
                $variant['code'],
                $variant['ean'],
                (float) $variant['stock'],
                $variant['unit'],
                (float) $variant['weight'],
                (float) $variant['width'],
                (float) $variant['height'],
                (float) $variant['depth'],
                $variant['visible'],
                (int) $variant['amountDecimalPlaces'],
                (float) $variant['price'],
                $variant['includingVat'],
                (float) $variant['vatRate'],
                $variant['currencyCode'],
                (float) $variant['actionPrice'],
                (float) $variant['commonPrice'],
                $availaility,
            );
            $productDetailResponse->addVariant($productVariantResponse);
        }
        return $productDetailResponse;
    }

    /**
     * @return array<ProductImageResponse>
     */
    public function getProductImages(): array
    {
        $result = [];
        if (ArrayHelper::containsKey($this->data, 'images') === false) {
            return $result;
        } 
        foreach ($this->data['images'] as $image) {
            $changeTime = null;
            $name = null;
            $priority = null;
            $description = null;
            $seoName = null;
            $cdnName = null;

            if (ArrayHelper::containsKey($image, 'name')) {
                $name = $image['name'];
            }
            if (ArrayHelper::containsKey($image, 'priority')) {
                $priority = $image['priority'];
            }
            if (ArrayHelper::containsKey($image, 'description')) {
                $description = $image['description'];
            }
            if (ArrayHelper::containsKey($image, 'changeTime') && $image['changeTime'] !== null) {
                $changeTime = new DateTime($image['changeTime']);
            }
            if (ArrayHelper::containsKey($image, 'seoName')) {
                $seoName = $image['seoName'];
            }
            if (ArrayHelper::containsKey($image, 'cdnName')) {
                $cdnName = $image['cdnName'];
            }
            $result[] = new ProductImageResponse($name, $priority, $seoName, $cdnName, $description, $changeTime);
        }
        return $result;
    }

    public function getEshop(): EshopResponse
    {
        $name = null;
        $title = null;
        $category = null;
        $subtitle = null;
        $ehopUrl = null;
        $contactPerson = null;
        $email = null;
        $phone = null;
        $street = null;
        $city = null;
        $zip = null;
        $country = null;
        $vatNumber = null;
        $oauthUrl = null;

        if (ArrayHelper::containsKey($this->data, 'contactInformation') === true) {
            $name = ResponseHelper::getFromResponse($this->data['contactInformation'], 'eshopName');
            $title = ResponseHelper::getFromResponse($this->data['contactInformation'], 'eshopTitle');
            $category = ResponseHelper::getFromResponse($this->data['contactInformation'], 'eshopCategory');
            $subtitle = ResponseHelper::getFromResponse($this->data['contactInformation'], 'eshopSubtitle');
            $ehopUrl = ResponseHelper::getFromResponse($this->data['contactInformation'], 'url');
            $contactPerson = ResponseHelper::getFromResponse($this->data['contactInformation'], 'contactPerson');
            $email = ResponseHelper::getFromResponse($this->data['contactInformation'], 'email');
            $phone = ResponseHelper::getFromResponse($this->data['contactInformation'], 'phone');
        }
        if (ArrayHelper::containsKey($this->data, 'address') === true) {
            $street = ResponseHelper::getFromResponse($this->data['address'], 'streetAddress');
            $city = ResponseHelper::getFromResponse($this->data['address'], 'city');
            $zip = ResponseHelper::getFromResponse($this->data['address'], 'zip');
        }
        if (ArrayHelper::containsKey($this->data, 'country') === true) {
            $country = ResponseHelper::getFromResponse($this->data['country'], 'countryCode');
        }
        if (ArrayHelper::containsKey($this->data, 'billingInformation') === true) {
            if (ArrayHelper::containsKey($this->data['billingInformation'], 'company')) {
                $vatNumber = ResponseHelper::getFromResponse($this->data['billingInformation']['company'], 'vatId');
            }
        }
        if (ArrayHelper::containsKey($this->data, 'urls') === true) {
            foreach ($this->data['urls'] as $url) {
                if (ArrayHelper::containsKey($url, 'ident') && $url['ident'] === 'oauth') {
                    $oauthUrl = ResponseHelper::getFromResponse($url, 'url');
                }
            }
        }
        return new EshopResponse($name, $title, $category, $subtitle, $ehopUrl, $contactPerson, $email, $phone, $street, $city, $zip, $country, $vatNumber, $oauthUrl);
    }

    public function postTemplateIncluded(): TemplateIncludeResponse
    {
        $templateIncludes = [];
        if (ArrayHelper::containsKey($this->data, 'snippets') === true) {
            foreach ($this->data['snippets'] as $snippet) {
                if (ArrayHelper::containsKey($snippet, 'location') === true) {
                    $location = $snippet['location'];
                }
                if (ArrayHelper::containsKey($snippet, 'html') === true) {
                    $html = $snippet['html'];
                }
                if (isset($location) && isset($html)) {
                    $templateIncludes[] = new TemplateIncludeSnippet($location, $html);
                }
            }    
        }
        return new TemplateIncludeResponse($templateIncludes);
    }

    /**
     * @return array<string, string>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function isInvalidToken(): bool
    {
        /** @var array<string, string> $error */
        foreach ($this->errors as $error) {
            if ($error['errorCode'] === 'invalid-token') {
                return true;
            }
        }
        return false;
    }
}