<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new OrderBuilder(SveaConfig::getDefaultConfig());  
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->orderRows = array( \TestUtil::createOrderRow(10.00) );                    
    }
    
    public function testClassExists() {
        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
        $this->assertInstanceOf('Svea\AddOrderRowsRequest', $AddOrderRowsRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {
        
        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }    

    public function test_validate_throws_exception_on_missing_OrderType() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );
        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }    
    
    public function test_validate_throws_exception_on_missing_CountryCode() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : countryCode is required.'
        );
        
        unset( $this->builderObject->countryCode );
        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }
    
    // todo broken test
//    public function test_validate_throws_exception_on_missing_orderRows() {
//
//        $this->setExpectedException(
//          'Svea\ValidationException', '-missing value : orderRows is required.'
//        );
//        
//        unset( $this->builderObject->orderRows );
//        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
//        $request = $AddOrderRowsRequestObject->prepareRequest();
//    }   
    
    public function test_validate_throws_exception_on_orderRows_missing_vat_information_none() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing order row vat information : cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.'
        );
        
        $this->builderObject->orderRows[] = \WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            //->setAmountExVat( 1.00 )
            //->setAmountIncVat( 1.00 * 1.25 ) 
            //->setVatPercent(25)
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setDiscountPercent(0)
        ;
        $AddOrderRowsRequestObject = new AddOrderRowsRequest( $this->builderObject );
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }     
}
