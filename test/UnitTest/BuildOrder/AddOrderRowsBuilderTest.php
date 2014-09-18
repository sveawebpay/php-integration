<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class AddOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $addOrderRowsObject;
    
    function setUp() {
        $this->addOrderRowsObject = new Svea\AddOrderRowsBuilder(Svea\SveaConfig::getDefaultConfig());  
    }
    
    public function test_addOrderRowsBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\AddOrderRowsBuilder", $this->addOrderRowsObject);
    }
    
    public function test_addOrderRowsBuilder_setOrderId() {
        $orderId = "123456";
        $this->addOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->addOrderRowsObject->orderId);        
    }
    
    public function test_addOrderRowsBuilder_setCountryCode() {
        $country = "SE";
        $this->addOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->addOrderRowsObject->countryCode);        
    }
    
    public function test_addOrderRowsBuilder_addInvoiceOrderRowsBuilder_returns_AddOrderRowsRequest() {
        $orderId = "123456";
        $addOrderRowsObject = $this->addOrderRowsObject
                ->setOrderId($orderId)
                ->addOrderRow( \TestUtil::createOrderRow(1.00) )
                ->addInvoiceOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\AddOrderRowsRequest", $addOrderRowsObject);
    }
    
    public function test_addOrderRowsBuilder_addPaymentPlanOrderRowsBuilder_returns_AddOrderRowsRequest() {
        $orderId = "123456";  
        $addOrderRowsObject = $this->addOrderRowsObject
                ->setOrderId($orderId)
                ->addOrderRow( \TestUtil::createOrderRow(1.00) )
                ->addPaymentPlanOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\AddOrderRowsRequest", $addOrderRowsObject);
    }
    
    public function test_addOrderRowsBuilder_missing_orderRows_throws_exception() {
        
        $this->setExpectedException('Svea\ValidationException');
        
        $orderId = "123456";
        $addOrderRowsObject = $this->addOrderRowsObject
                ->setOrderId($orderId)
                //->addOrderRow( \TestUtil::createOrderRow(1.00) )
                ->addInvoiceOrderRows();
        ;

        $addOrderRowsObject->doRequest();
    }
}
