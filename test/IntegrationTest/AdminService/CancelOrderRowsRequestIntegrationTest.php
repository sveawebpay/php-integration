<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsRequestIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function test_cancel_single_invoice_orderRow_()
    {

        // create order
        $country = "SE";

        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(24.00)
                ->setVatPercent(25)
            );

        $orderResponse = $order->useInvoicePayment()->doRequest();
        //print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);

        $myOrderId = $orderResponse->sveaOrderId;

        // cancel first row in order
        $cancelOrderRowsRequest = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $cancelOrderRowsRequest->setCountryCode($country);
        $cancelOrderRowsRequest->setOrderId($myOrderId);
        $cancelOrderRowsRequest->setRowToCancel(1);
        $cancelOrderRowsResponse = $cancelOrderRowsRequest->cancelInvoiceOrderRows()->doRequest();

        ////print_r( $cancelOrderRowsResponse );        
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\CancelOrderRowsResponse', $cancelOrderRowsResponse);
        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);
    }

    public function test_cancel_single_paymentplan_orderRow_()
    {

        // create order
        $country = "SE";
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();

        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(1600.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(2400.00)
                ->setVatPercent(25)
            );

        $orderResponse = $order->usePaymentPlanPayment($campaigncode)->doRequest();
        ////print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);

        $myOrderId = $orderResponse->sveaOrderId;

        // cancel first row in order                
        $cancelOrderRowsRequest = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $cancelOrderRowsRequest->setCountryCode($country);
        $cancelOrderRowsRequest->setOrderId($myOrderId);
        $cancelOrderRowsRequest->setRowToCancel(1);
        $cancelOrderRowsResponse = $cancelOrderRowsRequest->cancelPaymentPlanOrderRows()->doRequest();

        ////print_r( $cancelOrderRowsResponse );        
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\CancelOrderRowsResponse', $cancelOrderRowsResponse);
        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);
    }

    public function test_cancel_multiple_paymentplan_orderRows_()
    {

        // create order
        $country = "SE";
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();

        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(1600.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(2400.00)
                ->setVatPercent(25)
            );

        $orderResponse = $order->usePaymentPlanPayment($campaigncode)->doRequest();
        ////print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);

        $myOrderId = $orderResponse->sveaOrderId;

        // cancel first row in order                
        $cancelOrderRowsRequest = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $cancelOrderRowsRequest->setCountryCode($country);
        $cancelOrderRowsRequest->setOrderId($myOrderId);
        $cancelOrderRowsRequest->setRowsToCancel(array(1, 2));
        $cancelOrderRowsResponse = $cancelOrderRowsRequest->cancelPaymentPlanOrderRows()->doRequest();

        ////print_r( $cancelOrderRowsResponse );        
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\CancelOrderRowsResponse', $cancelOrderRowsResponse);
        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);
    }

}
