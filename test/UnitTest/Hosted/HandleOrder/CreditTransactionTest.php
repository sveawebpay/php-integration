<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreditTransactionTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->creditObject = WebPay::creditTransaction( Svea\SveaConfig::getDefaultConfig() );
    }

    // test methods
    function test_class_exists(){
        
        $this->assertInstanceOf( "Svea\CreditTransaction", $this->creditObject);      
    }
    
    function test_setTransactionId( ){
        
        $transactionId = 987654;       
        $this->creditObject->setTransactionId( $transactionId );
        
        //$this->assertEquals( $transactionId, $creditObject->transactionId );    //oops, transactionId is private.
        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'transactionId') );
    }
    
    function test_setCreditAmount() {
        
        $creditAmount = 100;
        $this->creditObject->setCreditAmount( $creditAmount );
        
        $this->assertEquals( $creditAmount, PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'creditAmount') );
    }
    
//    function test_prepareRequest() {
//
//        $transactionId = 987654;       
//        $this->creditObject->setTransactionId( $transactionId );
//
//        $creditAmount = 100;
//        $this->creditObject->setCreditAmount( $creditAmount );
//        
//        $xml = $this->prepareRequest();
//        
//    }
    
}

?>
