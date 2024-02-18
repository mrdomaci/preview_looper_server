<?php

declare(strict_types=1);

namespace App\Connector;

use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\DateTimeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\ClientService;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Request
{
    private const API_URL = 'https://api.myshoptet.com/api';

    /**
    * @param ClientService $clientService
    * @param string $method
    * @param string $endpoint
    * @param array<string, string> $query
    * @param string $body
    */
    public function __construct(
        private ClientService $clientService,
        private string $method = 'GET',
        private string $endpoint = '/products',
        private array $query = [],
        private ?string $body = null,
    ) {
    }

    public function getProducts(int $page): Request
    {
        $this->setMethod(Product::getMethod());
        $this->setEndpoint(Product::getEndpoint());
        $this->setPage($page);
        $this->setItemsPerPage(ResponseHelper::MAXIMUM_ITEMS_PER_PAGE);
        return $this;
    }

    public function getProductDetail(string $guid): Request
    {
        $this->setMethod(Product::getMethod());
        $this->setEndpoint(Product::getEndpoint($guid));
        $this->setQuery('include', 'images');
        return $this;
    }

    public function addFilterProducts(ProductFilter $productFilter): Request
    {
        $this->query[$productFilter->getKey()] = $productFilter->getValue();
        return $this;
    }

    public function getProductImages(string $guid, string $gallery): Request
    {
        $this->setMethod(ProductImages::getMethod());
        $this->setEndpoint(ProductImages::getEndpoint($guid, $gallery));
        return $this;
    }

    public function getEshop(): Request
    {
        $this->setMethod(Eshop::getMethod());
        $this->setEndpoint(Eshop::getEndpoint());
        return $this;
    }

    public function getOrderStatuses(): Request
    {
        $this->setMethod(OrderStatus::getMethod());
        $this->setEndpoint(OrderStatus::getEndpoint());
        return $this;
    }

    public function getOrders(int $page, ?DateTime $dateLastSynced): Request
    {
        $this->setMethod(Order::getMethod());
        $this->setEndpoint(Order::getEndpoint());
        if ($dateLastSynced !== null) {
            $this->setQuery('changeTimeFrom', DateTimeHelper::getDateTimeString($dateLastSynced));
        }
        $this->setPage($page);
        $this->setItemsPerPage(ResponseHelper::MAXIMUM_ITEMS_PER_PAGE);
        return $this;
    }

    public function getOrderDetail(string $code): Request
    {
        $this->setMethod(OrderDetail::getMethod());
        $this->setEndpoint(OrderDetail::getEndpoint($code));
        return $this;
    }

    public function postTemplateInclude(string $body): Request
    {
        $this->setMethod(TemplateInclude::getMethod());
        $this->setEndpoint(TemplateInclude::getEndpoint());
        $this->setBody($body);
        return $this;
    }

    public function getToken(): string
    {
        return $this->clientService->getAccessToken();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getQueryAsAString(): string
    {
        $result = '';
        $count = 0;
        foreach ($this->query as $key => $value) {
            if ($count === 0) {
                $result .= '?';
            } else {
                $result .= '&';
            }
            $count++;
            $result .= $key . '=' . $value;
        }
        return $result;
    }

    public function setQuery(string $key, string $value): void
    {
        $this->query[$key] = $value;
    }

    public function setPage(int $page): void
    {
        $this->setQuery('page', (string) $page);
    }

    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->setQuery('itemsPerPage', (string) $itemsPerPage);
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function send(): Response
    {
        try {
            $response = $this->sendRequest();
        } catch (\Throwable $e) {
            if ($e->getCode() === 401) {
                $this->clientService->setAttribute('access_token', TokenHelper::getApiAccessToken($this->clientService));
                $this->clientService->save();
                $response = $this->sendRequest();
            } else if ($e->getCode() === 404) {
                throw new ApiRequestNonExistingResourceException($e->getMessage(), 404);
            } else if ($e->getCode() === 429) {
                throw new ApiRequestTooManyRequestsException($e->getMessage(), 429);
            } else {
                throw new ApiRequestFailException(new Exception('API request failed for ' . self::API_URL . $this->endpoint . $this->getQueryAsAString() . ' with status code ' . $e->getCode() . ' and message ' . $e->getMessage()));
            }
        }

        return $this->parseResponse($response->getBody()->getContents());
    }

    private function sendRequest(): ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => [
                'Shoptet-Access-Token' => $this->clientService->getAccessToken(),
                'Content-Type' => 'application/vnd.shoptet.v1.0'
                ]
            ];
        if ($this->body !== null) {
            $options[\GuzzleHttp\RequestOptions::JSON] = json_decode($this->body);
        }
        return $client->request($this->method, self::API_URL . $this->endpoint . $this->getQueryAsAString(), $options);
    }

    private function parseResponse(string $response): Response
    {
        $response = json_decode($response, true);
        return new Response($response['data'], $response['errors']);
    }
}
