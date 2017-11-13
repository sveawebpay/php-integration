<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;


/**
 * Svea\WebPay\Test\IntegrationTest\BuildOrder\CreateOrderBuilderIntegrationTest holds all tests for how to build orders for diverse
 * payment methods.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CreateOrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase
{

    public function test_createOrder_Invoice_SE_Accepted()
    {
        $order = TestUtil::createOrder();
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    public function test_createOrder_Paymentplan_SE_Accepted()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate(date('c'));
        $response = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    // CreateOrderBuilder card payment method
    // see Svea\WebPay\Test\IntegrationTest\HostedService\Payment\CardPaymentURLIntegrationTest->test_manual_CardPayment_getPaymentUrl()

    // CreateOrderBuilder direct bank payment method   //TODO    


}


?>