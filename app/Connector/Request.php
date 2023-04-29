<?php
declare(strict_types=1);

namespace App\Connector;

use App\Exceptions\ApiRequestFailException;
use App\Helpers\TokenHelper;
use App\Models\Client;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Request
{
    private const API_URL = 'https://api.myshoptet.com/api';

    /**
     * @param Client $client
     * @param string $method
     * @param string $endpoint
     * @param array<string, string> $query
     */
    public function __construct(
        private Client $client,
        private string $method = 'GET',
        private string $endpoint = '/products',
        private array $query = [],
    ) {
    }

    public function getProducts(): Request
    {
        $this->setMethod(Product::getMethod());
        $this->setEndpoint(Product::getEndpoint());
        $this->setQuery(Product::getQuery());
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
        $this->setQuery(ProductImages::getQuery());
        return $this;
    }

    public function getEshop(): Request
    {
        $this->setMethod(Eshop::getMethod());
        $this->setEndpoint(Eshop::getEndpoint());
        $this->setQuery(Eshop::getQuery());
        return $this;
    }

    public function getToken(): string
    {
        return $this->client->getAttribute('access_token');
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
            if($count === 0) {
                $result .= '?';
            } else {
                $result .= '&';
            }
            $count++;        
            $result .= $key . '=' . $value;
        }
        return $result; 
    }

    /**
     * @param array<string, string> $query
     */
    public function setQuery(array $query): void
    {
        $this->query = $query;
    }


    public function send(): Response
    {
        try {
            $response = $this->sendRequest();
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->client->setAttribute('access_token', TokenHelper::getApiAccessToken($this->client));
                $this->client->save();
                $response = $this->sendRequest();
            } else {
                throw new ApiRequestFailException(new Exception('API request failed for ' . self::API_URL . $this->endpoint . $this->getQueryAsAString() . ' with status code ' . $e->getCode() . ' and message ' . $e->getMessage()));
            }
        }

        return $this->parseResponse($response->getBody()->getContents());
    }

    private function sendRequest(): ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $options = ['headers' => ['Shoptet-Access-Token' => $this->client->getAttribute('access_token'), 'Content-Type' => 'application/vnd.shoptet.v1.0']];
        return $client->request($this->method, self::API_URL . $this->endpoint . $this->getQueryAsAString(), $options);
    }

    private function parseResponse(string $response): Response
    {
        $response = json_decode($response, true);
        return new Response($response['data'], $response['errors']);
    }
}