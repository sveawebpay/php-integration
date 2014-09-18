<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new Svea\OrderBuilder(Svea\SveaConfig::getDefaultConfig());  
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToDeliver = array(1);
    }
    
    public function testClassExists() {
        $DeliverOrderRowsRequestObject = new Svea\AdminService\DeliverOrderRowsRequest( $this->builderObject );
        $this->assertInstanceOf('Svea\AdminService\DeliverOrderRowsRequest', $DeliverOrderRowsRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {
        
        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $DeliverOrderRowsRequestObject = new Svea\AdminService\DeliverOrderRowsRequest( $this->builderObject );
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }    

    public function test_validate_throws_exception_on_missing_OrderType() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );
        $DeliverOrderRowsRequestObject = new Svea\AdminService\DeliverOrderRowsRequest( $this->builderObject );
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }    
    
    public function test_validate_throws_exception_on_missing_CountryCode() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : countryCode is required.'
        );
        
        unset( $this->builderObject->countryCode );
        $DeliverOrderRowsRequestObject = new Svea\AdminService\DeliverOrderRowsRequest( $this->builderObject );
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }
    
    public function test_validate_throws_exception_on_missing_RowsToDeliver() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : rowsToDeliver is required.'
        );
        
        unset( $this->builderObject->rowsToDeliver );
        $DeliverOrderRowsRequestObject = new Svea\AdminService\DeliverOrderRowsRequest( $this->builderObject );
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }
}
