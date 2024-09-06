<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use App\Enums\QueueStatusEnum;
use App\Exceptions\ApiResponsePaginatorFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use DateTime;
use Exception;

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
            foreach ($errors as $key => $error) {
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
                if (ArrayHelper::isArray($product['brand']) &&  ArrayHelper::containsKey($product['brand'], 'code') && ArrayHelper::containsKey($product['brand'], 'name')) {
                    $brand = new ProductBrand(
                        $product['brand']['code'],
                        $product['brand']['name'],
                    );
                }
            }
            $productListResponse->addProduct(new ProductResponse($guid, $creationTime, $changeTime, $name, $voteAverageScore, $voteCount, $type, $visibility, $defaultCategory, $url, $supplier, $brand));
        }
        return $productListResponse;
    }

    public function getQueue(): ?QueueResponse
    {
        if (ArrayHelper::containsKey($this->data, 'jobId') === false) {
            return null;
        }
        return new QueueResponse($this->data['jobId']);
    }

    public function getQueueByJobId(): ?QueueResponse
    {
        if (ArrayHelper::containsKey($this->data, 'job') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['job'], 'jobId') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['job'], 'endpoint') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['job'], 'status') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['job'], 'resultUrl') === false) {
            return null;
        }
        return new QueueResponse(
            $this->data['job']['jobId'],
            $this->data['job']['endpoint'],
            (isset($this->data['job']['creationTime']) ? new DateTime($this->data['job']['creationTime']) : null),
            ($this->data['job']['duration'] ?? null),
            (isset($this->data['job']['completionTime']) ? new DateTime($this->data['job']['completionTime']) : null),
            QueueStatusEnum::fromCase($this->data['job']['status']),
            $this->data['job']['resultUrl'],
            (isset($this->data['job']['validUntil']) ? new DateTime($this->data['job']['validUntil']) : null),
            ($this->data['job']['log'] ?? null),
        );
    }

    public function getQueues(): ?JobListResponse
    {
        if (ArrayHelper::containsKey($this->data, 'jobs') === false) {
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

        $tempJobs = [];

        foreach ($this->data['jobs'] as $job) {
            $jobId = null;
            $endpoint = null;
            $creationTime = null;
            $completionTime = null;
            $status = null;
            $validUntil = null;

            if (ArrayHelper::containsKey($job, 'jobId') === false) {
                continue;
            } else {
                $jobId = $job['jobId'];
            }

            if (ArrayHelper::containsKey($job, 'endpoint') === false) {
                continue;
            } else {
                $endpoint = $job['endpoint'];
            }

            $resultUrl = (ArrayHelper::containsKey($job, 'resultUrl') ? $job['resultUrl'] : null);

            if (ArrayHelper::containsKey($job, 'creationTime') && $job['creationTime'] !== null) {
                $creationTime = new DateTime($job['creationTime']);
            }

            if (ArrayHelper::containsKey($job, 'completionTime') && $job['completionTime'] !== null) {
                $completionTime = new DateTime($job['completionTime']);
            }

            if (ArrayHelper::containsKey($job, 'status') === false) {
                continue;
            } else {
                $status = QueueStatusEnum::fromCase($job['status']);
            }

            if (ArrayHelper::containsKey($job, 'validUntil') && $job['validUntil'] !== null) {
                $validUntil = new DateTime($job['validUntil']);
            }

            $tempJobs[] = new JobResponse($jobId, $endpoint, $creationTime, $completionTime, $status, $validUntil, $resultUrl);
        }
        return new JobListResponse(
            $this->data['paginator']['totalCount'],
            $this->data['paginator']['page'],
            $this->data['paginator']['pageCount'],
            $this->data['paginator']['itemsOnPage'],
            $this->data['paginator']['itemsPerPage'],
            $tempJobs
        );
    }

    public function getOrders(): ?OrderListResponse
    {
        if (ArrayHelper::containsKey($this->data, 'orders') === false) {
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
        $orderListResponse = new OrderListResponse(
            $this->data['paginator']['totalCount'],
            $this->data['paginator']['page'],
            $this->data['paginator']['pageCount'],
            $this->data['paginator']['itemsOnPage'],
            $this->data['paginator']['itemsPerPage'],
            []
        );
        foreach ($this->data['orders'] as $order) {
            $code = null;
            $guid = null;
            $creationTime = null;
            $changeTime = null;
            $fullName = null;
            $company = null;
            $email = null;
            $phone = null;
            $remark = null;
            $cashDeskOrder = null;
            $customerGuid = null;
            $paid = null;
            $foreignStatusId = null;
            $source = null;
            $price = null;
            $paymentMethod = null;
            $shipping = null;
            $adminUrl = null;

            if (ArrayHelper::containsKey($order, 'code') === false) {
                continue;
            } else {
                $code = $order['code'];
            }

            if (ArrayHelper::containsKey($order, 'guid') === false) {
                continue;
            } else {
                $guid = $order['guid'];
            }

            if (ArrayHelper::containsKey($order, 'creationTime') && $order['creationTime'] !== null) {
                $creationTime = new DateTime($order['creationTime']);
            }

            if (ArrayHelper::containsKey($order, 'changeTime') && $order['changeTime'] !== null) {
                $changeTime = new DateTime($order['changeTime']);
            }

            if (ArrayHelper::containsKey($order, 'fullName')) {
                $fullName = $order['fullName'];
            }

            if (ArrayHelper::containsKey($order, 'company')) {
                $company = $order['company'];
            }

            if (ArrayHelper::containsKey($order, 'email')) {
                $email = $order['email'];
            }

            if (ArrayHelper::containsKey($order, 'phone')) {
                $phone = $order['phone'];
            }

            if (ArrayHelper::containsKey($order, 'remark')) {
                $remark = $order['remark'];
            }

            if (ArrayHelper::containsKey($order, 'cashDeskOrder')) {
                $cashDeskOrder = $order['cashDeskOrder'];
            } else {
                $cashDeskOrder = false;
            }

            if (ArrayHelper::containsKey($order, 'customerGuid')) {
                $customerGuid = $order['customerGuid'];
            }

            if (ArrayHelper::containsKey($order, 'paid')) {
                if ($order['paid'] === null) {
                    $paid = false;
                } else {
                    $paid = $order['paid'];
                }
            } else {
                $paid = false;
            }

            if (ArrayHelper::containsKey($order, 'status')) {
                if (ArrayHelper::isArray($order['status'])) {
                    $foreignStatusId = (string) $order['status']['id'];
                }
            }

            if (ArrayHelper::containsKey($order, 'source')) {
                if (ArrayHelper::isArray($order['source'])) {
                    $source = (string) $order['source']['id'];
                }
            }

            if (ArrayHelper::containsKey($order, 'price')) {
                if (ArrayHelper::isArray($order['price'])) {
                    $price = new OrderPriceResponse(
                        (float) $order['price']['vat'],
                        (float) $order['price']['toPay'],
                        $order['price']['currencyCode'],
                        (float) $order['price']['withVat'],
                        (float) $order['price']['withoutVat'],
                        (float) $order['price']['exchangeRate'],
                    );
                }
            }

            if (ArrayHelper::containsKey($order, 'paymentMethod')) {
                if (ArrayHelper::isArray($order['paymentMethod'])) {
                    $paymentMethod = new OrderPaymentMethodResponse(
                        $order['paymentMethod']['guid'],
                        $order['paymentMethod']['name'],
                    );
                }
            }

            if (ArrayHelper::containsKey($order, 'shipping')) {
                if (ArrayHelper::isArray($order['shipping'])) {
                    $shipping = new OrderShippingResponse(
                        $order['shipping']['guid'],
                        $order['shipping']['name'],
                    );
                }
            }

            if (ArrayHelper::containsKey($order, 'adminUrl')) {
                $adminUrl = $order['adminUrl'];
            }

            $orderListResponse->addOrder(new OrderResponse(
                (string) $code,
                $guid,
                $creationTime,
                $changeTime,
                $fullName,
                $company,
                $email,
                $phone,
                $remark,
                $cashDeskOrder,
                $customerGuid,
                $paid,
                $foreignStatusId,
                $source,
                $price,
                $paymentMethod,
                $shipping,
                $adminUrl,
            ));
        }
        return $orderListResponse;
    }

    public function getOrderDetails(): ?OrderDetailListResponse
    {
        if (ArrayHelper::containsKey($this->data, 'order') === false) {
            return null;
        }

        $orderDetailListResponse = new OrderDetailListResponse();

        if (ArrayHelper::containsKey($this->data['order'], 'items') === false) {
            return null;
        }

        foreach ($this->data['order']['items'] as $item) {
            if (ArrayHelper::containsKey($item, 'productGuid') === false) {
                continue;
            }
            if (ArrayHelper::containsKey($item, 'amount') === false) {
                continue;
            }
            if ($item['productGuid'] === null) {
                continue;
            }
            if ($item['amount'] === null) {
                continue;
            }
            $orderDetailResponse = new OrderDetailResponse(
                $item['productGuid'],
                (float) $item['amount'],
            );
            $orderDetailListResponse->addProduct($orderDetailResponse);
        }

        return $orderDetailListResponse;
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
            if (ArrayHelper::isArray($this->data['brand']) &&  ArrayHelper::containsKey($this->data['brand'], 'code') && ArrayHelper::containsKey($this->data['brand'], 'name')) {
                $brand = new ProductBrand(
                    $this->data['brand']['code'],
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

        $productDetailResponse = new ProductDetailResponse(
            $guid,
            $creationTime,
            $changeTime,
            $name,
            $voteAverageScore,
            $voteCount,
            $type,
            $visibility,
            $defaultCategory,
            $url,
            $supplier,
            $brand,
            $perex,
        );

        $lowesPriority = 1000000;
        $imageUrl = null;
        foreach ($this->data['images'] as $image) {
            $changeTime = null;
            if (ArrayHelper::containsKey($image, 'changeTime') && $image['changeTime'] !== null) {
                $changeTime = new DateTime($image['changeTime']);
            }
            $productImageResponse = new ProductImageResponse(
                $image['name'],
                $image['priority'] ? (int) $image['priority'] : null,
                $image['seoName'],
                $image['cdnName'],
                $image['description'],
                $changeTime,
            );
            if ($productImageResponse->getPriority() < $lowesPriority) {
                $lowesPriority = $productImageResponse->getPriority();
                $imageUrl = $productImageResponse->getCdnName();
            }
            $productDetailResponse->addImage($productImageResponse);
        }
        $productDetailResponse->setImageUrl(StringHelper::removeParameter($imageUrl));

        foreach ($this->data['variants'] as $variant) {
            $availability = null;
            $availabilityId = null;
            $isNegativeStockAllowed = false;
            $stock = (float) $variant['stock'];
            if (ArrayHelper::containsKey($variant, 'availability') === true) {
                if ($variant['availability'] !== null) {
                    $availability = $variant['availability']['name'];
                    $availabilityId = (string) $variant['availability']['id'];
                }
            }
            if ($availability === null && $stock <= 0) {
                if (ArrayHelper::containsKey($variant, 'availabilityWhenSoldOut') === true) {
                    if ($variant['availabilityWhenSoldOut'] !== null) {
                        $availability = $variant['availabilityWhenSoldOut']['name'];
                        $availabilityId = (string) $variant['availabilityWhenSoldOut']['id'];
                    }
                }
            }
            $variantName = $name;
            if (ArrayHelper::containsKey($variant, 'name')) {
                $variantName .= ' ' . $variant['name'];
            }
            if (ArrayHelper::containsKey($variant, 'negativeStockAllowed')) {
                if ($variant['negativeStockAllowed'] === 'yes') {
                    $isNegativeStockAllowed = true;
                } elseif ($variant['negativeStockAllowed'] === 'yes-global') {
                    $isNegativeStockAllowed = true;
                }
            }
            $image = StringHelper::removeParameter($variant['image']);
            $foreignId = StringHelper::getIdFromImage($image);

            $productVariantResponse = new ProductVariantResponse(
                $variant['code'],
                $variant['ean'],
                $stock,
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
                $availability,
                $variantName,
                $availabilityId,
                $image,
                $foreignId,
                $isNegativeStockAllowed,
            );
            $productDetailResponse->addVariant($productVariantResponse);
        }

        if (ArrayHelper::containsKey($this->data, 'categories')) {
            foreach ($this->data['categories'] as $category) {
                $productCategory = new ProductCategory($category['guid'], $category['name']);
                $productDetailResponse->addCategory($productCategory);
            }
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

    public function postWebhook(): bool
    {
        return true;
    }

    public function getAvailabilities(): ?AvailabilityListResponse
    {
        $onStockId = null;
        $soldOutNegativeStockAllowedId = null;
        $soldOutNegativeStockForbiddenId = null;
        if (ArrayHelper::containsKey($this->data, 'defaultAvailabilities') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data, 'availabilities') === false) {
            return null;
        }
        if (ArrayHelper::containsKey($this->data['defaultAvailabilities'], 'onStock')) {
            $onStockId = (string) $this->data['defaultAvailabilities']['onStock'];
        }
        if (ArrayHelper::containsKey($this->data['defaultAvailabilities'], 'soldOutNegativeStockAllowed')) {
            $soldOutNegativeStockAllowedId = (string) $this->data['defaultAvailabilities']['soldOutNegativeStockAllowed'];
        }
        if (ArrayHelper::containsKey($this->data['defaultAvailabilities'], 'soldOutNegativeStockForbidden')) {
            $soldOutNegativeStockForbiddenId = (string) $this->data['defaultAvailabilities']['soldOutNegativeStockForbidden'];
        }
        $availabilityListResponse = new AvailabilityListResponse([], $onStockId, $soldOutNegativeStockAllowedId, $soldOutNegativeStockForbiddenId);
        foreach ($this->data['availabilities'] as $availability) {
            $onStockInHours = null;
            $deliveryInHours = null;
            $color = null;
            $description = null;
            $isSystem = false;
            $googleAvailability = null;
            if (ArrayHelper::containsKey($availability, 'name') === false) {
                continue;
            }
            $name = $availability['name'];
            if (ArrayHelper::containsKey($availability, 'id') === false) {
                continue;
            }
            $id = $availability['id'];
            if (ArrayHelper::containsKey($availability, 'system')) {
                $isSystem = $availability['system'];
            }
            if (ArrayHelper::containsKey($availability, 'description')) {
                $description = $availability['description'];
            }
            if (ArrayHelper::containsKey($availability, 'onStockInHours') && $availability['onStockInHours'] !== null) {
                $onStockInHours = (string) $availability['onStockInHours'];
            }
            if (ArrayHelper::containsKey($availability, 'deliveryInHours') && $availability['deliveryInHours'] !== null) {
                $deliveryInHours = (string) $availability['deliveryInHours'];
            }
            if (ArrayHelper::containsKey($availability, 'color')) {
                $color = $availability['color'];
            }
            if (ArrayHelper::containsKey($availability, 'googleAvailability')
                && ArrayHelper::isArray($availability['googleAvailability'])
                && ArrayHelper::containsKey($availability['googleAvailability'], 'id')
                && ArrayHelper::containsKey($availability['googleAvailability'], 'name')) {
                $googleAvailability = new GoogleAvailabilityResponse(
                    (string) $availability['googleAvailability']['id'],
                    (string) $availability['googleAvailability']['name']
                );
            }
            $availabilityListResponse->addAvailability(
                new AvailabilityResponse(
                    (string) $id,
                    $name,
                    $isSystem,
                    $description,
                    $onStockInHours,
                    $deliveryInHours,
                    $color,
                    $googleAvailability
                )
            );
        }
        return $availabilityListResponse;
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
