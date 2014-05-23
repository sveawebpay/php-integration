<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $cancelOrderRowsObject;
    
    function setUp() {
        $this->cancelOrderRowsObject = new CancelOrderRowsBuilder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_cancelOrderRowsBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\cancelOrderRowsBuilder", $this->cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_setOrderId() {
        $orderId = "123456";
        $this->cancelOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->cancelOrderRowsObject->orderId);        
    }
    
    public function test_cancelOrderRowsBuilder_setCountryCode() {
        $country = "SE";
        $this->cancelOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->cancelOrderRowsObject->countryCode);        
    }
    
    public function test_cancelOrderRowsBuilder_setOrderType() {
        $orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->cancelOrderRowsObject->setOrderType($orderType);
        $this->assertEquals($orderType, $this->cancelOrderRowsObject->orderType);        
    }
    
    public function test_cancelOrderRowsBuilder_cancelInvoiceOrderRowsBuilder_returns_CancelOrderRowsRequest() {
        $orderId = "123456";
        $cancelOrderRowsObject = $this->cancelOrderRowsObject->setOrderId($orderId)->cancelInvoiceOrderRows();
        
        $this->assertInstanceOf("Svea\CancelOrderRowsRequest", $cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_cancelPaymentPlanOrderRowsBuilder_returns_CancelOrderRowsRequest() {
        $orderId = "123456";  
        $cancelOrderRowsObject = $this->cancelOrderRowsObject->setOrderId($orderId)->cancelPaymentPlanOrderRows();
        
        $this->assertInstanceOf("Svea\CancelOrderRowsRequest", $cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_cancelCardOrderRowsBuilder_returns_LowerTransaction() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'todo'
        );
        
        
        $orderId = "123456";
        $cancelOrderRowsObject = $this->cancelOrderRowsObject->setOrderId($orderId)->cancelCardOrderRows();
        
        $this->assertInstanceOf("Svea\LowerTransaction", $cancelOrderRowsObject);
    }  
}
