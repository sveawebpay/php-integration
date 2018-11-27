<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CancelOrderBuilderIntegrationTest extends \PHPUnit\Framework\TestCase
{

    // CancelOrderBuilder endpoints: cancelInvoiceOrder(), cancelPaymentPlanOrder(), cancelCardOrder()
    function test_CancelOrderBuilder_Invoice_success()
    {
        $country = "SE";
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country));
        $orderResponse = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);

        $cancelResponse = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->cancelInvoiceOrder()
            ->doRequest();

        $this->assertEquals(1, $cancelResponse->accepted);
    }

    function test_CancelOrderBuilder_PaymentPlan_success()
    {
        $country = "SE";
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            );
        $orderResponse = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);

        $cancelResponse = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->cancelPaymentPlanOrder()
            ->doRequest();

        $this->assertEquals(1, $cancelResponse->accepted);
    }

    /**
     * test_manual_CancelOrderBuilder_Card_success
     *
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */
    function test_manual_CancelOrderBuilder_Card_success()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of cancelOrder for a card order'
        );

        // Set the below to match the transaction, then run the test.
        $customerrefno = "test_1396964349955";
        $transactionId = 580658;

        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($transactionId)
            ->setCountryCode("SE")
            ->cancelCardOrder()
            ->doRequest();

        $this->assertInstanceOf('Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\HostedAdminResponse', $response);

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($customerrefno, $response->customerrefno);
    }

}

