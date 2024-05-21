<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpService
{
    public function __construct(private HttpClientInterface $httpClient)
    {}

    public function get(string $url)
    {
        $response = $this->httpClient->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error fetching data: ' . $response->getStatusCode() . ' ' . $response->getContent());
        }

        return $response->toArray();
    }
}