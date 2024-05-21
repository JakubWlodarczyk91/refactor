<?php

namespace App\Tests;

use App\Service\HttpService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpServiceTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClientMock;
    private ResponseInterface&MockObject $responseMock;


    public function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);
    }

    public function testGetSuccessfulResponse()
    {

        $this->responseMock->method('getStatusCode')->willReturn(200);
        $this->responseMock->method('toArray')->willReturn(['key' => 'value']);
        $this->responseMock->method('getContent')->willReturn(json_encode(['key' => 'value']));

        $this->httpClientMock->method('request')->willReturn($this->responseMock);

        $httpService = new HttpService($this->httpClientMock);

        $result = $httpService->get('http://example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('value', $result['key']);
    }

    public function testGetErrorResponse()
    {

        $this->responseMock->method('getStatusCode')->willReturn(404);
        $this->responseMock->method('getContent')->willReturn('Not Found');

        $this->httpClientMock->method('request')->willReturn($this->responseMock);

        $httpService = new HttpService($this->httpClientMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error fetching data: 404 Not Found');

        $httpService->get('http://example.com');
    }
}
