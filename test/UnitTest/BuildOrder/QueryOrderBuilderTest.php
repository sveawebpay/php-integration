<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class QueryOrderBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $queryOrderObject;
    
    function setUp() {
        $this->queryOrderObject = new QueryOrderBuilder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_queryOrderBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\queryOrderBuilder", $this->queryOrderObject);
    }
    
    public function test_queryOrderBuilder_setOrderId() {
        $orderId = "123456";
        $this->queryOrderObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->queryOrderObject->orderId);        
    }
    
    public function test_queryOrderBuilder_setCountryCode() {
        $country = "SE";
        $this->queryOrderObject->setCountryCode($country);
        $this->assertEquals($country, $this->queryOrderObject->countryCode);        
    }
    
    public function test_queryOrderBuilder_setPaymentMethod_INVOICE_returns_QueryOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::INVOICE;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...
        
        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryInvoiceOrder();
        
        $this->assertInstanceOf("Svea\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $queryOrderObject->orderBuilder->orderType);

    }
    
    public function test_queryOrderBuilder_setPaymentMethod_PAYMENTPLAN_returns_QueryOrder_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::PAYMENTPLAN;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...
        
        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryPaymentPlanOrder();
        
        $this->assertInstanceOf("Svea\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $queryOrderObject->orderBuilder->orderType);

    }
    
//    public function test_queryOrderBuilder_setPaymentMethod_KORTCERT_returns_CloseOrder() {
//        $orderId = "123456";
//        $paymentMethod = \PaymentMethod::KORTCERT;
//        
//        $annulTransactionObject = $this->queryOrderObject->setOrderId($orderId)->cancelCardOrder();
//        
//        $this->assertInstanceOf("Svea\AnnulTransaction", $annulTransactionObject);
//    }
//    
}
