<?php
namespace Svea\WebPay\Test\IntegrationTest\WebService\GetPaymentPlanParams;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebService\GetPaymentPlanParams\GetPaymentPlanParams as GetPaymentPlanParams;

/**
 * @author Jonas Lith
 */
class GetPaymentPlanParamsIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function testPaymentPlanParamsResult()
    {
        $config = ConfigurationService::getDefaultConfig();
        $paymentPlanRequest = new GetPaymentPlanParams($config);
        $request = $paymentPlanRequest
            ->setCountryCode("SE")
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    public function testResultGetPaymentPlanParams()
    {
        $config = ConfigurationService::getDefaultConfig();
        $paymentPlanRequest = new GetPaymentPlanParams($config);
        $request = $paymentPlanRequest
            ->setCountryCode("SE")
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(213060, $request->campaignCodes[0]->campaignCode);
        $this->assertEquals('Dela upp betalningen på 60 månader', $request->campaignCodes[0]->description);
        $this->assertEquals('Standard', $request->campaignCodes[0]->paymentPlanType);
        $this->assertEquals(60, $request->campaignCodes[0]->contractLengthInMonths);
        $this->assertEquals(0.02555, $request->campaignCodes[0]->monthlyAnnuityFactor);
        $this->assertEquals(100, $request->campaignCodes[0]->initialFee);
        $this->assertEquals(29, $request->campaignCodes[0]->notificationFee);
        $this->assertEquals(16.75, $request->campaignCodes[0]->interestRatePercent);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfInterestFreeMonths);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfPaymentFreeMonths);
        $this->assertEquals(1000, $request->campaignCodes[0]->fromAmount);
        $this->assertEquals(50000, $request->campaignCodes[0]->toAmount);
    }

    //outcommented cause need to use client with only one campaign to test
//    public function testResultGetPaymentPlanParams_only_one_campaign() {
//
//        $paymentPlanRequest = new GetPaymentPlanParams(Svea\WebPay\Config\ConfigurationService::getTestConfig());
//        $request = $paymentPlanRequest
//                ->setCountryCode("SE")
//                ->doRequest();
//        print_r($request);
//        $this->assertEquals(1, $request->accepted);
//        $this->assertEquals(0, $request->resultcode);
//        $this->assertEquals(213060, $request->campaignCodes[0]->campaignCode);
//        $this->assertEquals('Köp nu betala om 3 månader (räntefritt)', $request->campaignCodes[0]->description);
//        $this->assertEquals('InterestAndAmortizationFree', $request->campaignCodes[0]->paymentPlanType);
//        $this->assertEquals(3, $request->campaignCodes[0]->contractLengthInMonths);
//        $this->assertEquals(100, $request->campaignCodes[0]->initialFee);
//        $this->assertEquals(29, $request->campaignCodes[0]->notificationFee);
//        $this->assertEquals(0, $request->campaignCodes[0]->interestRatePercent);
//        $this->assertEquals(3, $request->campaignCodes[0]->numberOfInterestFreeMonths);
//        $this->assertEquals(3, $request->campaignCodes[0]->numberOfPaymentFreeMonths);
//        $this->assertEquals(1000, $request->campaignCodes[0]->fromAmount);
//        $this->assertEquals(50000, $request->campaignCodes[0]->toAmount);
//    }
}
