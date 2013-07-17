<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../test/UnitTest/Hosted/Payment/FakeHostedPayment.php';

class HostedXmlBuilderTest extends PHPUnit_Framework_TestCase {
    
    public function testFormatOrderRows() {
        $order = new CreateOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $payment = new FakeHostedPayment($order);
        $payment->order = $order;
        $payment->setCancelUrl("http://www.cancel.com");
        
        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $order);
        
        $this->assertEquals(1, substr_count($xml, "http://www.cancel.com"));
    }
}

?>
