<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * Description of PaymentPlanParamsTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanParamsTest extends PHPUnit_Framework_TestCase {

    function testBuildRequest() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                //->setTestmode()()
                //->setPasswordBasedAuthorization("sverigetest", "sverigetest", 59999)
                ->setCountryCode("SE")
                ->prepareRequest();
        //doRequest();
        $this->assertEquals(59999, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
    }
    
    

}

?>
