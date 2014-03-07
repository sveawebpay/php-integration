<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class GetPaymentMethodTest extends PHPUnit_Framework_TestCase {

    function testGetAllPaymentMethods(){
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::getPaymentMethods($config)
                ->setContryCode("SE")
                ->prepareRequest();

        $this->assertEquals(1130, $request['merchantid']);
        $this->assertNotEmpty($request['message']);
        $this->assertNotEmpty($request['mac']);

    }
}

?>
