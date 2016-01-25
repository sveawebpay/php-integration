<?php
use \Svea\WebService\GetPaymentPlanParams as GetPaymentPlanParams;
use \Svea\WebService\PaymentPlanPricePerMonth as PaymentPlanPricePerMonth;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith, Kristian Grossman-Madsen
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
//        $this->assertEquals(213060, $response->values[0]['campaignCode']);//don't test to be flexible
        $this->assertEquals(2029, $response->values[0]['pricePerMonth']);
    }

    function testBuildPriceCalculatorWithLowPrice_should_not_return_anything_if_price_is_less_than_all_campaign_min_prices() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(99,$params);
        $this->assertEmpty($response->values);
    }

    function testBuildPriceCalculatorWithLowPrice_should_return_prices_if_IgnoreCampaignMinAndMax_flag_is_set() {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(200,$params, true);
        $this->assertNotEmpty($response->values);
    }
}
