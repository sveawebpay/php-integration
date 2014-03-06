<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreditTransactionTest extends PHPUnit_Framework_TestCase {

    function test_class_exists(){
        $config = Svea\SveaConfig::getDefaultConfig();

        $creditObject = WebPay::creditTransaction($config);
        
        $this->assertInstanceOf( "Svea\CreditTransaction", $creditObject);      
    }
    
    function test_setTransactionId( ){
        $config = Svea\SveaConfig::getDefaultConfig();

        $creditObject = WebPay::creditTransaction($config);

        $transactionId = 987654;
        
        $creditObject->setTransactionId( $transactionId );
        
        //$this->assertEquals( $transactionId, $creditObject->transactionId );    //oops, transactionId is private.
        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($creditObject, 'transactionId') );
    }
    
    function test_setCreditAmount() {
        $config = Svea\SveaConfig::getDefaultConfig();

        $creditObject = WebPay::creditTransaction($config);

        $creditAmount = 100;
        
        $creditObject->setCreditAmount( $creditAmount );
        
        $this->assertEquals( $creditAmount, PHPUnit_Framework_Assert::readAttribute($creditObject, 'creditAmount') );
    }
}

?>
