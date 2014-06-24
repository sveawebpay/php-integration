<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $cancelOrderObject;
    
    function setUp() {
        $this->cancelOrderObject = new Svea\CancelOrderBuilder(Svea\SveaConfig::getDefaultConfig());  
    }
    
    public function test_CancelOrderBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\CancelOrderBuilder", $this->cancelOrderObject);
    }
    
    public function test_CancelOrderBuilder_setOrderId() {
        $orderId = "123456";
        $this->cancelOrderObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->cancelOrderObject->orderId);        
    }
    
    public function test_CancelOrderBuilder_setCountryCode() {
        $country = "SE";
        $this->cancelOrderObject->setCountryCode($country);
        $this->assertEquals($country, $this->cancelOrderObject->countryCode);        
    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_INVOICE_returns_CloseOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::INVOICE;
        
        $closeOrderObject = $this->cancelOrderObject->setOrderId($orderId)->cancelInvoiceOrder();
        
        $this->assertInstanceOf("Svea\WebService\CloseOrder", $closeOrderObject);
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $closeOrderObject->orderBuilder->orderType);

    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_PAYMENTPLAN_returns_CloseOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::PAYMENTPLAN;
        
        $closeOrderObject = $this->cancelOrderObject->setOrderId($orderId)->cancelPaymentPlanOrder();
        
        $this->assertInstanceOf("Svea\WebService\CloseOrder", $closeOrderObject);
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $closeOrderObject->orderBuilder->orderType);
    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_KORTCERT_returns_CloseOrder() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::KORTCERT;
        
        $annulTransactionObject = $this->cancelOrderObject->setOrderId($orderId)->cancelCardOrder();
        
        $this->assertInstanceOf("Svea\HostedService\AnnulTransaction", $annulTransactionObject);
    }
    
}
