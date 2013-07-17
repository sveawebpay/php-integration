<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class PaymentPlanPricePerMonthTest extends PHPUnit_Framework_TestCase {
    
    private function getGetPaymentPlanParamsResponseForTesting() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $response = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();
        return $response;
    }

    public function testBuildPriceCalculator() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = WebPay::paymentPlanPricePerMonth(2000,$params);
        $this->assertEquals(213060, $response->values[0]['campaignCode']);
        $this->assertEquals(2029, $response->values[0]['pricePerMonth']);
    }
    
    function testBuildPriceCalculatorWithLowPrice() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = WebPay::paymentPlanPricePerMonth(200,$params);
        $this->assertEmpty($response->values);
    }
}

?>
