<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new Svea\DeliverOrderBuilder(Svea\SveaConfig::getDefaultConfig());  
        $this->builderObject->setOrderId(123456);
        $this->builderObject->setCountryCode("SE");
        $this->builderObject->setInvoiceDistributionType(\DistributionType::POST);        
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;                                                        
    }
    
    public function testClassExists() {
        $deliverOrdersRequestObject = new Svea\AdminService\DeliverOrdersRequest(new Svea\DeliverOrderBuilder(Svea\SveaConfig::getDefaultConfig()));
        $this->assertInstanceOf('Svea\AdminService\DeliverOrdersRequest', $deliverOrdersRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_DistributionType() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : distributionType is required.'
        );
        
        unset( $this->builderObject->distributionType );

        $deliverOrderRequestObject = new Svea\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );

        $deliverOrderRequestObject = new Svea\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType() {
        
        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );

        $deliverOrderRequestObject = new Svea\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }      
}
