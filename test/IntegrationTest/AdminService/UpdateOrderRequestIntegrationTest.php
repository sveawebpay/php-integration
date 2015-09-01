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

    public function test_add_new_notes_invoice() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
                ->setNotes('Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                    penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                    Donec qu')//string 200 chars
                ->updateInvoiceOrder()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                    penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                    Donec qu', $query2->notes);
        //print_r($orderResponse->sveaOrderId);
    }

    //TODO: Where to put this test

    public function test_add_new_notes_too_long_invoice() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

        try {
                    $response = WebPayAdmin::updateOrder($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->setNotes('Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                    penatibus et magnis dis parturient montes, nascetur ridiculus mus. D
                    onec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.
                    Nulla consequat massa quis enim. Donec.')//string 300 chars
                ->updateInvoiceOrder()->doRequest();
        } catch (Exception $exc) {
            print_r($exc->getMessage());
        }

        $this->assertEquals(1, $response->accepted);


    }

}