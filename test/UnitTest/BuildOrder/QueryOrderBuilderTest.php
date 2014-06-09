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
    
    public function test_queryOrderBuilder_setOrderType() {
        $orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->queryOrderObject->setOrderType($orderType);
        $this->assertEquals($orderType, $this->queryOrderObject->orderType);        
    }
    
    public function test_queryOrderBuilder_queryInvoiceOrder_returns_GetOrdersRequest_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \ConfigurationProvider::INVOICE_TYPE;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...
        
        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryInvoiceOrder();
        
        $this->assertInstanceOf("Svea\AdminService\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals($paymentMethod, $queryOrderObject->orderBuilder->orderType);

    }
    
    public function test_queryOrderBuilder_queryPaymentPlanOrder_returns_GetOrdersRequest_with_correct_orderType() {
        $orderId = "123456";
        $paymentMethod = \ConfigurationProvider::PAYMENTPLAN_TYPE;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...
        
        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryPaymentPlanOrder();
        
        $this->assertInstanceOf("Svea\AdminService\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals($paymentMethod, $queryOrderObject->orderBuilder->orderType);

    }
    
    public function test_queryOrderBuilder_queryCardOrder_returns_QueryTransaction() {
        $orderId = "123456";
        
        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryCardOrder();
        
        $this->assertInstanceOf("Svea\HostedService\QueryTransaction", $queryOrderObject);
    }  
}
