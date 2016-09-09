<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\Helper;

use Svea\WebPay\Test\UnitTest\HostedService\Payment\FakeHostedPayment;
use Svea\WebPay\WebPay;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\CreateOrderBuilder;
use Svea\WebPay\BuildOrder\RowBuilders\OrderRow;
use Svea\WebPay\Config\SveaConfigurationProvider;
use Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer;
use Svea\WebPay\HostedService\Helper\HostedXmlBuilder as HostedXmlBuilder;


class HostedXmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    private $order;

    protected function setUp()
    {
        $this->order = WebPay::createOrder(ConfigurationService::getDefaultConfig());

        $this->individualCustomer = new IndividualCustomer();
        $this->individualCustomer->setNationalIdNumber("123456");

        $this->orderRow = new OrderRow();
        $this->orderRow->setAmountExVat(100.00);
        $this->orderRow->setVatPercent(25);
        $this->orderRow->setQuantity(2);
    }

    public function testBasicXml()
    {
        $this->order->setClientOrderNumber("1234")
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->addCustomerDetails($this->individualCustomer)
            ->addOrderRow($this->orderRow);

        $payment = new FakeHostedPayment($this->order);
        $payment->order = $this->order;
        $payment->setReturnUrl("http://myurl.se");

        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $this->order);

        $this->assertEquals(1, substr_count($xml, "<payment>"));
        $this->assertEquals(1, substr_count($xml, "<customerrefno>1234</customerrefno>"));
        $this->assertEquals(1, substr_count($xml, "<currency>SEK</currency>"));
        $this->assertEquals(1, substr_count($xml, "<amount>25000</amount>"));
        $this->assertEquals(1, substr_count($xml, "<vat>5000</vat>"));
        $this->assertEquals(1, substr_count($xml, "<addinvoicefee>FALSE</addinvoicefee>"));
        $this->assertEquals(1, substr_count($xml, "<lang>en</lang>"));
        $this->assertEquals(1, substr_count($xml, '<returnurl>http://myurl.se</returnurl>'));
        $this->assertEquals(1, substr_count($xml, "<cancelurl/>"));
        $this->assertEquals(1, substr_count($xml, "<customer>"));
        $this->assertEquals(1, substr_count($xml, "<ssn>123456</ssn>"));
        $this->assertEquals(1, substr_count($xml, "<country>SE</country>"));
        $this->assertEquals(1, substr_count($xml, "</customer>"));
        $this->assertEquals(1, substr_count($xml, "<iscompany>FALSE</iscompany>"));
        $this->assertEquals(1, substr_count($xml, "<orderrows>"));
        $this->assertEquals(1, substr_count($xml, "<row>"));
        $this->assertEquals(1, substr_count($xml, "<sku></sku>"));
        $this->assertEquals(1, substr_count($xml, "<name></name>"));
        $this->assertEquals(1, substr_count($xml, "<description></description>"));
        $this->assertEquals(1, substr_count($xml, "<amount>12500</amount>"));
        $this->assertEquals(1, substr_count($xml, "<vat>2500</vat>"));
        $this->assertEquals(1, substr_count($xml, "<quantity>2</quantity>"));
        $this->assertEquals(1, substr_count($xml, "</row>"));
        $this->assertEquals(1, substr_count($xml, "</orderrows>"));
        $this->assertEquals(1, substr_count($xml, "</payment>"));
    }

    public function testXmlWithIndividualCustomer()
    {
        $customer = $this->individualCustomer;
        $customer->setName("Julius", "Caesar");
        $customer->setInitials("JS");
        $customer->setPhoneNumber("999999");
        $customer->setEmail("test@svea.com");
        $customer->setIpAddress("123.123.123.123");
        $customer->setStreetAddress("Gatan", "23");
        $customer->setCoAddress("c/o Eriksson");
        $customer->setZipCode("9999");
        $customer->setLocality("Stan");

        $this->order = new CreateOrderBuilder(new SveaConfigurationProvider(ConfigurationService::getDefaultConfig()));
        $this->order->setClientOrderNumber("1234")
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->addCustomerDetails($customer)
            ->addOrderRow($this->orderRow);

        $payment = new FakeHostedPayment($this->order);
        $payment->order = $this->order;
        $payment->setReturnUrl("http://myurl.se");

        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $this->order);

        $this->assertEquals(1, substr_count($xml, "<ssn>123456</ssn>"));
        $this->assertEquals(1, substr_count($xml, "<firstname>Julius</firstname>"));
        $this->assertEquals(1, substr_count($xml, "<lastname>Caesar</lastname>"));
        $this->assertEquals(1, substr_count($xml, "<initials>JS</initials>"));
        $this->assertEquals(1, substr_count($xml, "<phone>999999</phone>"));
        $this->assertEquals(1, substr_count($xml, "<email>test@svea.com</email>"));
        $this->assertEquals(1, substr_count($xml, "<address>Gatan</address>"));
        $this->assertEquals(1, substr_count($xml, "<housenumber>23</housenumber>"));
        $this->assertEquals(1, substr_count($xml, "<address2>c/o Eriksson</address2>"));
        $this->assertEquals(1, substr_count($xml, "<zip>9999</zip>"));
        $this->assertEquals(1, substr_count($xml, "<city>Stan</city>"));
        $this->assertEquals(1, substr_count($xml, "<country>SE</country>"));
    }

    public function testXmlWithOrderRow()
    {
        $row = $this->orderRow;
        $row->setArticleNumber("1");
        $row->setName("Product");
        $row->setDescription("Good product");
        $row->setUnit("kg");

        $this->order->setClientOrderNumber("1234")
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->addCustomerDetails($this->individualCustomer)
            ->addOrderRow($row);

        $payment = new FakeHostedPayment($this->order);
        $payment->order = $this->order;
        $payment->setReturnUrl("http://myurl.se");

        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $this->order);

        $this->assertEquals(1, substr_count($xml, "<sku>1</sku>"));
        $this->assertEquals(1, substr_count($xml, "<name>Product</name>"));
        $this->assertEquals(1, substr_count($xml, "<description>Good product</description>"));
        $this->assertEquals(1, substr_count($xml, "<unit>kg</unit>"));
    }

    public function testXmlWithCancelUrl()
    {
        $payment = new FakeHostedPayment($this->order);
        $payment->setCancelUrl("http://www.cancel.com");

        $xmlBuilder = new HostedXmlBuilder();
        $xml = $xmlBuilder->getOrderXML($payment->calculateRequestValues(), $this->order);

        $this->assertEquals(1, substr_count($xml, "http://www.cancel.com"));
    }

//    public function test_getCreditTransactionXML() {
//        
//        // example from webservice api docs
//        $elements = array( 
//            "transactionid" => 521527,
//            "amounttocredit" => 100
//        );
//        
//        // generate the request XML
//        $xmlBuilder = new HostedXmlBuilder();
//        $requestXML = $xmlBuilder->getCreditTransactionXML( $elements );
//
//        // parse the generated request XML
//        $xmlMessage = new \SimpleXMLElement($requestXML);
//  
//        $this->assertEquals((string)$elements["transactionid"], $xmlMessage->transactionid);
//        $this->assertEquals((string)$elements["amounttocredit"], $xmlMessage->amounttocredit);
//    }  

//    public function test_getQueryTransactionXML() {
//        
//        // example from webservice api docs
//        $elements = array( 
//            "transactionid" => 521527,
//        );
//        
//        // generate the request XML
//        $xmlBuilder = new HostedXmlBuilder();
//        $requestXML = $xmlBuilder->getQueryTransactionXML( $elements );
//
//        // parse the generated request XML
//        $xmlMessage = new \SimpleXMLElement($requestXML);
//  
//        $this->assertEquals((string)$elements["transactionid"], $xmlMessage->transactionid);
//    }  
}
