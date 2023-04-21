<?php
declare(strict_types=1);

namespace App\Connector;

use App\Exceptions\ApiResponsePaginatorFailException;
use App\Helpers\ArrayHelper;
use DateTime;
use Exception;

class Response
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string>|null $errors
     */
    public function __construct(
        private array $data,
        private ?array $errors = null,
    ) {
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

    /**
     * @return array<ProductResponse>
     */
    public function getProducts(): array
    {
        $result = [];
        if (ArrayHelper::containsKey($this->data, 'products') === false) {
            return $result;
        }
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
                if (ArrayHelper::isArray($product['defaultCategory']) && ArrayHelper::containsKey($product['defaultCategory'], 'guid') && ArrayHelper::containsKey($product['defaultCategory'], 'name')) {
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
            $result[] = new ProductResponse($guid, $creationTime, $changeTime, $name, $voteAverageScore, $voteCount, $type, $visibility, $defaultCategory, $url, $supplier, $brand);
        }
        return $result;
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

    /**
     * @return array<string, string>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}