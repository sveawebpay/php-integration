<?php
namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedAdminRequest\AnnulTransaction as AnnulTransaction;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\AnnulTransactionIntegrationTest
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AnnulTransactionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * test_annul_card_transaction_not_found
     *
     * used as initial acceptance criteria for annul transaction feature
     */
    function test_annul_card_transaction_not_found()
    {

        $transactionId = 987654;

        $request = new AnnulTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\AnnulTransactionResponse", $response);

        // if we receive an error from the service, the integration test passes
        $this->assertEquals(0, $response->accepted);
        $this->assertEquals("128 (NO_SUCH_TRANS)", $response->resultcode);
    }

    /**
     * test_manual_annul_card
     *
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */
    function test_manual_annul_card()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of annul card transaction' // TODO
        );

        // Set the below to match the transaction, then run the test.
        $customerrefno = "794";
        $transactionId = 587947;

        $request = new AnnulTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        //print_r( $response); 
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\AnnulTransactionResponse", $response);
        $this->assertEquals(1, $response->accepted);
        $this->assertStringMatchesFormat("%d", $response->transactionId);   // %d => an unsigned integer value

        $this->assertEquals($customerrefno, $response->clientOrderNumber);
    }
}

?>
