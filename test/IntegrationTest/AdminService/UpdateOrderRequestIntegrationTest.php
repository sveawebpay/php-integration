<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRequestIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public $notes = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                    penatibus et magnis';

    public function test_add_new_clientordernumber_invoice()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->setClientOrderNumber('123')//string
            ->updateInvoiceOrder()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals('123', $query2->clientOrderId);
        //print_r($orderResponse->sveaOrderId);
    }

    public function test_add_new_notes_invoice()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->setNotes($this->notes)//string 200 chars
            ->updateInvoiceOrder()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals($this->notes, $query2->notes);
        //print_r($orderResponse->sveaOrderId);
    }

}