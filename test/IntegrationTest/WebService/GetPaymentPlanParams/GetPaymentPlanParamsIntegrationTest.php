<?php
use \Svea\WebService\GetPaymentPlanParams as GetPaymentPlanParams;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class GetPaymentPlanParamsIntegrationTest extends PHPUnit_Framework_TestCase {

    public function testPaymentPlanParamsResult() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $paymentPlanRequest = new GetPaymentPlanParams($config);
        $request = $paymentPlanRequest
                ->setCountryCode("SE")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    public function testResultGetPaymentPlanParams() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $paymentPlanRequest = new GetPaymentPlanParams($config);
        $request = $paymentPlanRequest
                ->setCountryCode("SE")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(213060, $request->campaignCodes[0]->campaignCode);
        $this->assertEquals('Köp nu betala om 3 månader (räntefritt)', $request->campaignCodes[0]->description);
        $this->assertEquals('InterestAndAmortizationFree', $request->campaignCodes[0]->paymentPlanType);
        $this->assertEquals(3, $request->campaignCodes[0]->contractLengthInMonths);
        $this->assertEquals(100, $request->campaignCodes[0]->initialFee);
        $this->assertEquals(29, $request->campaignCodes[0]->notificationFee);
        $this->assertEquals(0, $request->campaignCodes[0]->interestRatePercent);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfInterestFreeMonths);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfPaymentFreeMonths);
        $this->assertEquals(1000, $request->campaignCodes[0]->fromAmount);
        $this->assertEquals(50000, $request->campaignCodes[0]->toAmount);
    }

    //outcommented cause need to use client with only one campaign to test
//    public function testResultGetPaymentPlanParams_only_one_campaign() {
//
//        $paymentPlanRequest = new GetPaymentPlanParams(\Svea\SveaConfig::getTestConfig());
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
