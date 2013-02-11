<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once '/../Payment/FakeHostedPayment.php';

/**
 * Description of HostedXmlBuilderTest
 */
class HostedXmlBuilderTest extends PHPUnit_Framework_TestCase {
    
    public function testFormatOrderRows() {
        $order = new createOrder();
        $payment = new FakeHostedPayment($order);
        $payment->order = $order;
        $payment->setCancelUrl("http://www.cancel.com");
        
        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $order);
        
        $this->assertEquals(1, substr_count($xml, "http://www.cancel.com"));
    }
}

?>
