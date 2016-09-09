<?php

namespace Svea\WebPay\Test\UnitTest\WebService\GetPaymentPlanParams;

use Svea\WebPay\WebPay;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanParamsTest extends PHPUnit_Framework_TestCase
{

    public function testBuildRequest()
    {
        $config = ConfigurationService::getDefaultConfig();
        $addressRequest = WebPay::getPaymentPlanParams($config);
        $request = $addressRequest
            ->setCountryCode("SE")
            ->prepareRequest();

        $this->assertEquals(59999, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
    }
}
