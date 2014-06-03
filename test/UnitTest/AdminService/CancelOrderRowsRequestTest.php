<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new Svea\OrderBuilder(Svea\SveaConfig::getDefaultConfig());  
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToCancel = array(1);
    }
    
    public function testClassExists() {
        $CancelOrderRowsRequestObject = new Svea\AdminService\CancelOrderRowsRequest( $this->builderObject );
        $this->assertInstanceOf('Svea\AdminService\CancelOrderRowsRequest', $CancelOrderRowsRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {
        
        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $CancelOrderRowsRequestObject = new Svea\AdminService\CancelOrderRowsRequest( $this->builderObject );
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }    

    public function test_validate_throws_exception_on_missing_OrderType() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );
        $CancelOrderRowsRequestObject = new Svea\AdminService\CancelOrderRowsRequest( $this->builderObject );
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }    
    
    public function test_validate_throws_exception_on_missing_CountryCode() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : countryCode is required.'
        );
        
        unset( $this->builderObject->countryCode );
        $CancelOrderRowsRequestObject = new Svea\AdminService\CancelOrderRowsRequest( $this->builderObject );
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }
    
    public function test_validate_throws_exception_on_missing_RowsToCancel() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : rowsToCancel is required.'
        );
        
        unset( $this->builderObject->rowsToCancel );
        $CancelOrderRowsRequestObject = new Svea\AdminService\CancelOrderRowsRequest( $this->builderObject );
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }
}
