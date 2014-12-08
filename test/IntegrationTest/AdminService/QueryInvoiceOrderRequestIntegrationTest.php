<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class QueryOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    function test_orderrow_response_incvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

        $response = WebPayAdmin::queryOrder($config)
              ->setCountryCode('SE')
              ->setOrderId($orderResponse->sveaOrderId)
              ->queryInvoiceOrder()
                ->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
       $this->assertEquals(145.00, $response->numberedOrderRows[0]->amountIncVat);
       $this->assertEquals(null, $response->numberedOrderRows[0]->amountExVat);

    }
    function test_orderrow_response_exvat() {
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
                ->setCurrency("SEK")
                    ->setOrderDate("2012-12-12")
                    ->useInvoicePayment()
                        ->doRequest();

        $response = WebPayAdmin::queryOrder($config)
              ->setCountryCode('SE')
              ->setOrderId($orderResponse->sveaOrderId)
              ->queryInvoiceOrder()
                ->doRequest();
//        print_r($response);
       $this->assertEquals(1, $response->accepted);
        $this->assertEquals(145.00, $response->numberedOrderRows[0]->amountExVat);
       $this->assertEquals(null, $response->numberedOrderRows[0]->amountIncVat);

    }

}