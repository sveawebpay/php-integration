<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $updateOrderRowsObject;
    
    function setUp() {
        $this->updateOrderRowsObject = new UpdateOrderRowsBuilder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_updateOrderRowsBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\UpdateOrderRowsBuilder", $this->updateOrderRowsObject);
    }
    
    public function test_updateOrderRowsBuilder_setOrderId() {
        $orderId = "123456";
        $this->updateOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->updateOrderRowsObject->orderId);        
    }
    
    public function test_updateOrderRowsBuilder_setCountryCode() {
        $country = "SE";
        $this->updateOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->updateOrderRowsObject->countryCode);        
    }
    
    public function test_updateOrderRowsBuilder_setOrderType() {
        $orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->updateOrderRowsObject->setOrderType($orderType);
        $this->assertEquals($orderType, $this->updateOrderRowsObject->orderType);        
    }
    
    public function test_updateOrderRowsBuilder_updateInvoiceOrderRowsBuilder_returns_UpdateOrderRowsRequest() {
        $orderId = "123456";
        $updateOrderRowsObject = $this->updateOrderRowsObject->setOrderId($orderId)->updateInvoiceOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\UpdateOrderRowsRequest", $updateOrderRowsObject);
    }
    
    public function test_updateOrderRowsBuilder_updatePaymentPlanOrderRowsBuilder_returns_UpdateOrderRowsRequest() {
        $orderId = "123456";  
        $updateOrderRowsObject = $this->updateOrderRowsObject->setOrderId($orderId)->updatePaymentPlanOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\UpdateOrderRowsRequest", $updateOrderRowsObject);
    }
}
