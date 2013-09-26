<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../test/UnitTest/Hosted/Payment/FakeHostedPayment.php';

class HostedXmlBuilderTest extends \PHPUnit_Framework_TestCase {
    
    public function testBasicXml() {
        $customer = new IndividualCustomer();
        $customer->setNationalIdNumber("123456");
        
        $orderRow = new OrderRow();
        $orderRow->setAmountExVat(100.00);
        $orderRow->setVatPercent(25);
        $orderRow->setQuantity(2);
        
        $order = new CreateOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->setClientOrderNumber("1234")
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->addCustomerDetails($customer)
                ->addOrderRow($orderRow);
        
        $payment = new FakeHostedPayment($order);
        $payment->order = $order;
        $payment->setReturnUrl("http://myurl.se");
        
        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $order);
        
        $this->assertEquals(1, substr_count($xml, "<payment>"));
        $this->assertEquals(1, substr_count($xml, "<customerrefno>1234</customerrefno>"));
        $this->assertEquals(1, substr_count($xml, '<returnurl>http://myurl.se</returnurl>'));
        $this->assertEquals(1, substr_count($xml, "<cancelurl/>"));
        $this->assertEquals(1, substr_count($xml, "<amount>25000</amount>"));
        $this->assertEquals(1, substr_count($xml, "<currency>SEK</currency>"));
        $this->assertEquals(1, substr_count($xml, "<lang/>"));
        $this->assertEquals(1, substr_count($xml, "<addinvoicefee>FALSE</addinvoicefee>"));
        $this->assertEquals(1, substr_count($xml, "<customer>"));
        $this->assertEquals(1, substr_count($xml, "<ssn>123456</ssn>"));
        $this->assertEquals(1, substr_count($xml, "<country>SE</country>"));
        $this->assertEquals(1, substr_count($xml, "</customer>"));
        $this->assertEquals(1, substr_count($xml, "<vat>5000</vat>"));
        $this->assertEquals(1, substr_count($xml, "<orderrows>"));
        $this->assertEquals(1, substr_count($xml, "<row>"));
        $this->assertEquals(1, substr_count($xml, "<description></description>"));
        $this->assertEquals(1, substr_count($xml, "<name></name>"));
        $this->assertEquals(1, substr_count($xml, "<sku></sku>"));
        $this->assertEquals(1, substr_count($xml, "<amount>12500</amount>"));
        $this->assertEquals(1, substr_count($xml, "<vat>2500</vat>"));
        $this->assertEquals(1, substr_count($xml, "<quantity>2</quantity>"));
        $this->assertEquals(1, substr_count($xml, "</row>"));
        $this->assertEquals(1, substr_count($xml, "</orderrows>"));
        $this->assertEquals(1, substr_count($xml, "<iscompany>FALSE</iscompany>"));
        $this->assertEquals(1, substr_count($xml, "</payment>"));
    }
    
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
