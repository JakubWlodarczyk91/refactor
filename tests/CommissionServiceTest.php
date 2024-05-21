<?php

namespace App\Tests\Service;

use App\Service\CommissionService;
use App\Service\HttpService;
use App\Service\PublicFileReadService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    private PublicFileReadService&MockObject $publicFileReadServiceMock;
    private HttpService&MockObject $httpServiceMock;

    public function setUp(): void
    {
        $this->publicFileReadServiceMock = $this->createMock(PublicFileReadService::class);
        $this->httpServiceMock = $this->createMock(HttpService::class);
    }
    public function testGetCommissionForEU()
    {

        $singleData = ['bin' => '123456', 'amount' => 100, 'currency' => 'EUR'];
        $rateData = ['rates' => ['EUR' => 1.0]];

        $binData = ['country' => ['alpha2' => 'FR']];

        $this->httpServiceMock->expects($this->once())->method('get')->willReturn($binData);

        $commissionService = new CommissionService($this->publicFileReadServiceMock, $this->httpServiceMock);

        $commission = $this->invokeMethod($commissionService, 'getCommission', [$singleData, $rateData]);

        $this->assertEquals(1, $commission);
    }

    public function testGetCommissionForNonEU()
    {

        $singleData = ['bin' => '654321', 'amount' => 200, 'currency' => 'USD'];
        $rateData = ['rates' => ['USD' => 1.5]];

        $commissionService = new CommissionService($this->publicFileReadServiceMock, $this->httpServiceMock);

        $commission = $this->invokeMethod($commissionService, 'getCommission', [$singleData, $rateData]);

        $this->assertEquals(2.67, $commission, '', 0.00001);
    }

    public function testGetCommissionForNonEUBinInEU()
    {
        $singleData = ['bin' => '123456', 'amount' => 100, 'currency' => 'USD'];
        $rateData = ['rates' => ['USD' => 1.5]];

        $binData = ['country' => ['alpha2' => 'FR']];

        $this->httpServiceMock->expects($this->once())->method('get')->willReturn($binData);

        $commissionService = new CommissionService($this->publicFileReadServiceMock, $this->httpServiceMock);

        $commission = $this->invokeMethod($commissionService, 'getCommission', [$singleData, $rateData]);

        $this->assertEquals(0.67, $commission);
    }

    public function testGetCommissionForNonEUBinNonEU()
    {
        $singleData = ['bin' => '654321', 'amount' => 200, 'currency' => 'USD'];
        $rateData = ['rates' => ['USD' => 1.5]];

        $binData = ['country' => ['alpha2' => 'US']];

        $this->httpServiceMock->expects($this->once())->method('get')->willReturn($binData);

        $commissionService = new CommissionService($this->publicFileReadServiceMock, $this->httpServiceMock);

        $commission = $this->invokeMethod($commissionService, 'getCommission', [$singleData, $rateData]);

        $this->assertEquals(2.67, $commission);
    }

    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}