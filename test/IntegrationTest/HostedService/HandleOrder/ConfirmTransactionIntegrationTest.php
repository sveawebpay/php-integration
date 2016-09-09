<?php

namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction as ConfirmTransaction;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\ConfirmTransactionIntegrationTest
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class ConfirmTransactionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * test_confirmTransaction_card_success creates an order using card payment,
     * pays using card & receives a transaction id, then confirms the transaction
     *
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function test_confirmTransaction_card_success()
    {

        // not yet implemented, requires webdriver support

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'not yet implemented, requires webdriver support'
        );

        // also, needs to have SUCCESS status set on transaction

        // set up order (from testUtil?)
        $order = TestUtil::createOrder();

        // pay with card, receive transactionId
        $form = $order
            ->UsePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.se")
            //->setCancelUrl()
            //->setCardPageLanguage("SE")
            ->getPaymentForm();

        $url = "https://test.sveaekonomi.se/webpay/payment";

        // do request modeled on Svea\WebPay\Test\IntegrationTest\HostedService\Payment\CardPaymentIntegrationTest.php

        // make sure the transaction has status AUTHORIZED at Svea

        // confirm transcation using above the transaction transactionId

        // assert response from confirmTransaction equals success
    }


    /**
     * test_confirm_card_transaction_not_found
     *
     * used as initial acceptance criteria for credit transaction feature
     */
    function test_confirm_card_transaction_not_found()
    {

        $transactionId = 987654;
        $captureDate = "2014-03-21";

        $request = new ConfirmTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->captureDate = $captureDate;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $response);

        // if we receive an error from the service, the integration test passes
        $this->assertEquals(0, $response->accepted);
        $this->assertEquals("128 (NO_SUCH_TRANS)", $response->resultcode);
    }

    /**
     * test_manual_credit_card
     *
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */
    function test_manual_confirm_card()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of confirm card transaction'
        );

        // Set the below to match the transaction, then run the test.
        $clientOrderNumber = "798";
        $transactionId = 587950;
        $captureDate = date('c');

        $request = new ConfirmTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->captureDate = $captureDate;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        //print_r( $response );
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $response);
        $this->assertEquals(1, $response->accepted);
        $this->assertStringMatchesFormat("%d", $response->transactionId);   // %d => an unsigned integer value

        $this->assertEquals($clientOrderNumber, $response->clientOrderNumber);
    }
}

?>
