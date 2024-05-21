<?php
namespace App\Service;
use App\Enum\CountryEnum;

class CommissionService
{
    public function __construct(private PublicFileReadService $publicFileReadService, private HttpService $httpService)
    {}

    private function getCommission(array $singleData, array $rateData): float
    {
        $binUrl = 'https://lookup.binlist.net/' . $singleData['bin'];
        $binData = $this->httpService->get($binUrl);
        $commission = 0;

        if (CountryEnum::isEu($singleData['currency'])){
            $commission = $singleData['amount'];
        } else {
            $rate = $rateData['rates'][$singleData['currency']];
            if ($rate > 0) {
                $commission = $singleData['amount'] / $rate;
            }
        }

        if (!empty($binData['country']['alpha2']) && CountryEnum::isEu($binData['country']['alpha2'])){
            $commission *= 0.01;
        } else {
            $commission *= 0.02;
        }

        return ceil($commission*100) / 100;
    }

    public function getCommissions(): array
    {
        $data = $this->publicFileReadService->read('input.txt');
        $rateUrl = 'http://api.exchangeratesapi.io/latest?access_key=55df560509e2ee2b0711067c3a6edca5';
        $rateData = $this->httpService->get($rateUrl);

        $commissionArray = [];

        foreach ($data as $singleData) {
            $commissionArray[] = $this->getCommission($singleData, $rateData);
        }

        return $commissionArray;
    }

}
