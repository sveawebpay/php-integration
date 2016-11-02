<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRowsRequestTest extends \PHPUnit_Framework_TestCase
{

    public function test_add_single_orderRow_as_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
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

    public function test_add_single_orderRow_as_incvat_and_vatpercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
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

    public function test_add_single_orderRow_as_incvat_and_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
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

    public function test_add_single_orderRow_mixed_types_1()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->prepareRequest();
//      print_r($request);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_single_orderRow_mixed_types_2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setAmountIncVat(123.9876)
                ->setQuantity(1)
            )
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->prepareRequest();
//      print_r($request);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_single_orderRow_mixed_types_3()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setAmountIncVat(123.9876)
                ->setQuantity(1)
            )
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->prepareRequest();
//      print_r($request);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->UpdatedOrderRows->enc_value->enc_value[2]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->UpdatedOrderRows->enc_value->enc_value[2]->enc_value->PriceIncludingVat->enc_value);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : rowNumber is required
     */
    public function test_add_single_orderRow_missing_rownumber()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId('test')
            ->updateOrderRow(WebPayItem::numberedOrderRow()
//                        ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setAmountIncVat(123.9876)
                ->setQuantity(1)
            )
            ->updateInvoiceOrderRows()
            ->prepareRequest();
//      print_r($request);
    }

}