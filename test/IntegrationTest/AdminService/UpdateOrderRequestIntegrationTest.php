<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRequestIntegrationTest extends PHPUnit_Framework_TestCase {

    public function test_add_new_clientordernumber_invoice() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountIncVat(123.9876)
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrder($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->setClientOrderNumber('123')//string
                ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals('123', $query2->clientOrderNumber);
        //print_r($orderResponse->sveaOrderId);
    }
    
    public function test_add_new_notes_invoice() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountIncVat(123.9876)
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrder($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->setNotes('My notes 123')//string
                ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals('My notes 123', $query2->Notes);
        //print_r($orderResponse->sveaOrderId);
    }

}