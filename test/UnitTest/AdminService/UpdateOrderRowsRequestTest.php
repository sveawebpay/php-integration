<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    public function test_add_single_orderRow_as_exvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
      $request = WebPayAdmin::updateOrderRows($config)
              ->setCountryCode('SE')
              ->setOrderId('test')
              ->updateOrderRow(    WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setVatPercent(24)
                        ->setAmountExVat(80.00)
                        ->setQuantity(1)
                   )
              ->updateInvoiceOrderRows()
              ->prepareRequest();
        $this->assertEquals(80, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_single_orderRow_as_incvat_and_vatpercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
              ->setCountryCode('SE')
              ->setOrderId('test')
              ->updateOrderRow(    WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setVatPercent(24)
                        ->setAmountIncVat(123.9876)
                        ->setQuantity(1)
                   )
              ->updateInvoiceOrderRows()
              ->prepareRequest();
//      print_r($request);
         $this->assertEquals(123.9876, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }
    public function test_add_single_orderRow_as_incvat_and_exvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
              ->setCountryCode('SE')
              ->setOrderId('test')
              ->updateOrderRow(    WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setAmountExVat(99.99)
                        ->setAmountIncVat(123.9876)
                        ->setQuantity(1)
                   )
              ->updateInvoiceOrderRows()
              ->prepareRequest();
//      print_r($request);
         $this->assertEquals(123.9876, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }
}