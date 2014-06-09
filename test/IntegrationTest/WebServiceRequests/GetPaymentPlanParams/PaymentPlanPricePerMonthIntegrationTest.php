<?php
use \Svea\WebService\GetPaymentPlanParams as GetPaymentPlanParams;
use \Svea\WebService\PaymentPlanPricePerMonth as PaymentPlanPricePerMonth;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class PaymentPlanPricePerMonthTest extends PHPUnit_Framework_TestCase {

    private function getGetPaymentPlanParamsResponseForTesting() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = new GetPaymentPlanParams($config);
        $response = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();
        return $response;
    }

    public function testBuildPriceCalculator() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(2000,$params);
        $this->assertEquals(213060, $response->values[0]['campaignCode']);
        $this->assertEquals(2029, $response->values[0]['pricePerMonth']);
    }

    function testBuildPriceCalculatorWithLowPrice() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(200,$params);
        $this->assertEmpty($response->values);
    }
}
