<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use App\Exceptions\BankApiRequestFailException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Request
{
    private const FIO_API_URL = 'https://fioapi.fio.cz/v1/rest/periods';

    /**
    * @param string $method
    * @param string $endpoint
    * @param array<string, string> $query
    * @param string $body
    */
    public function __construct(
        private string $method = 'GET',
        private string $endpoint = '/',
        private array $query = [],
        private ?string $body = null,
    ) {
    }

    public function getLicense(DateTime $from, DateTime $to, string $currency): Request
    {
        $this->setMethod(License::getMethod());
        $this->setEndpoint(License::getEndpoint($from, $to, $currency));
        return $this;
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

    public function sendFio(): Response
    {
        $response = $this->sendFioRequest();
        return $this->parseResponse($response->getBody()->getContents());
    }

    private function sendFioRequest(): ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => [
                ]
            ];
        if ($this->body !== null) {
            $options[\GuzzleHttp\RequestOptions::JSON] = json_decode($this->body);
        }
        return $client->request($this->method, self::FIO_API_URL . $this->endpoint . $this->getQueryAsAString(), $options);
    }

    private function parseResponse(string $response): Response
    {
        $response = json_decode($response, true);
        if (isset($response['accountStatement']) === false) {
            throw new BankApiRequestFailException(new Exception('Bank api response returned invalid data'));
        }
        return new Response($response['accountStatement']);
    }
}
