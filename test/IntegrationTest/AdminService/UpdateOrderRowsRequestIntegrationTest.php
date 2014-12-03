<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{

        public function test_add_single_orderRow() {
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
        $this->assertEquals(1, $orderResponse->accepted);

      $response = WebPayAdmin::updateOrderRows($config)
              ->setCountryCode('SE')
              ->setOrderId($orderResponse->sveaOrderId)
              ->updateOrderRow(    WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setVatPercent(24)
                        ->setAmountExVat(80.00)
                        ->setQuantity(1)
                   )
              ->updateInvoiceOrderRows()
              ->doRequest();

        $this->assertEquals(1, $response->accepted );
    }
}