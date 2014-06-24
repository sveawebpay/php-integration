<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebService/svea_soap/SveaSoapConfig.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanParamsTest extends PHPUnit_Framework_TestCase {

    public function testBuildRequest() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getPaymentPlanParams($config);
        $request = $addressRequest
                ->setCountryCode("SE")
                ->prepareRequest();

        $this->assertEquals(59999, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
    }
}
