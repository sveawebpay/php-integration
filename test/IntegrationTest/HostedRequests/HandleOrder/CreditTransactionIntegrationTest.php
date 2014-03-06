<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * CreditTransactionIntegrationTest creates an order using card payment, receives a transaction
 * 
 * used as acceptance criteria/smoke test for credit transaction feature (card)
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {

    function test_Card_CreditTransaction() {

        $config = Svea\SveaConfig::getDefaultConfig();
        
        // set up order (from testUtil?)
        
        // pay with card, receive transactionId
        
        // credit transcation with above transactionId
        
        $response = WebPay::creditTransaction($config)
            ->setTransactionId("SE")
            ->doRequest();

        // asserts return from function
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response[0]);

        // ? check that the credit transaction has gone through to Svea -- available to integration test?
        
    }
}
?>
