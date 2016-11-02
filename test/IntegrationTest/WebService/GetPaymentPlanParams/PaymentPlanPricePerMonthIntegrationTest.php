<?php
namespace Svea\WebPay\Test\IntegrationTest\WebService\GetPaymentPlanParams;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebService\GetPaymentPlanParams\GetPaymentPlanParams as GetPaymentPlanParams;
use Svea\WebPay\WebService\GetPaymentPlanParams\PaymentPlanPricePerMonth as PaymentPlanPricePerMonth;


/**
 * @author Jonas Lith, Kristian Grossman-Madsen
 */
class PaymentPlanPricePerMonthTest extends PHPUnit_Framework_TestCase
{

    private function getGetPaymentPlanParamsResponseForTesting()
    {
        $config = ConfigurationService::getDefaultConfig();
        $addressRequest = new GetPaymentPlanParams($config);
        $response = $addressRequest
            ->setCountryCode("SE")
            ->doRequest();

        return $response;
    }

    public function testBuildPriceCalculator()
    {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(2000, $params);
//        $this->assertEquals(213060, $response->values[0]['campaignCode']);//don't test to be flexible
        $this->assertEquals(2029, $response->values[0]['pricePerMonth']);
    }

    function testBuildPriceCalculatorWithLowPrice_should_not_return_anything_if_price_is_less_than_all_campaign_min_prices()
    {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(99, $params);
        $this->assertEmpty($response->values);
    }

    function testBuildPriceCalculatorWithLowPrice_should_return_prices_if_IgnoreCampaignMinAndMax_flag_is_set()
    {
        $params = $this->getGetPaymentPlanParamsResponseForTesting();
        $response = new PaymentPlanPricePerMonth(200, $params, true);
        $this->assertNotEmpty($response->values);
    }
}
