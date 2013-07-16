<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class GetPaymentPlanParamsTest extends PHPUnit_Framework_TestCase {

    function testPaymentPlanParamsResult() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }
}

?>
