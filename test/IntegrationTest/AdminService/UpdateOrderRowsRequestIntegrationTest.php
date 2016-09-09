<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase
{

    public function test_update_orderRow_as_exvat_and_vatpercent()
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
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setVatPercent(24)
                ->setAmountExVat(80.00)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("80.00", $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }

    public function test_update_orderRow_as_incvat_and_vatpercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setVatPercent(24)
                ->setAmountIncVat(80.00)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("80.00", $query->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }

    public function test_update_orderRow_as_incvat_and_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("123.99", $query->numberedOrderRows[0]->amountIncVat);      // 123.9876 => 123.99
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setAmountIncVat(123.9876)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals("123.99", $query2->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals("24", $query2->numberedOrderRows[0]->vatPercent);
    }

    public function test_UpdateOrderRows_created_exvat_updated_incvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);   // 123.99/1.24 = 99.99
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }

    public function test_UpdateOrderRows_created_exvat_updated_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("123.99", $query->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }

    public function test_UpdateOrderRows_created_incvat_updated_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("123.99", $query->numberedOrderRows[0]->amountIncVat);  // sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals("123.99", $query2->numberedOrderRows[0]->amountIncVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query2->numberedOrderRows[0]->vatPercent);
        //print_r($orderResponse->sveaOrderId);
    }

    public function test_add_single_orderRow_type_missmatch_3()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    public function test_add_single_orderRow_type_mismatch_created_inc_updated_ex()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals("123.99", $query->numberedOrderRows[0]->amountIncVat);  // sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);

        $response = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals("123.99", $query2->numberedOrderRows[0]->amountIncVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query2->numberedOrderRows[0]->vatPercent);
        //print_r($orderResponse->sveaOrderId);
    }

}