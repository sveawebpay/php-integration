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
}

?>
