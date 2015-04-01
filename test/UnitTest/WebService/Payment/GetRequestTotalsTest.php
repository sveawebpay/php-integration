<?php
$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

class GetRequestTotalsTest extends PHPUnit_Framework_TestCase {

    function test_get_invoice_total_amount_before_createorder() {
       $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK");
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setName('Universal Camera Charger')
                ->setAmountIncVat(19.60)
                ->setVatPercent(25)
                ->setQuantity(100)
                )
                ->addFee(\WebPayItem::invoiceFee()
                    ->setAmountIncVat(29.00)
                    ->setVatPercent(25)
                    ->setName('Svea Invoice Fee')
                )
                ->addDiscount(
                \WebPayItem::fixedDiscount()
                    ->setAmountIncVat(294.00)
                    ->setName('Discount')
                )
        ;
        $total = $order->useInvoicePayment()->getRequestTotals();

         $this->assertEquals( 1695.0, $total['total_incvat'] );
         $this->assertEquals( 1356.0, $total['total_exvat'] );
         $this->assertEquals( 339.0, $total['total_vat'] );
    }

    function test_get_invoice_total_amount_before_createorder_creates_discount_rows_using_incvat_and_vatpercent() {
       $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK");
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setName('Universal Camera Charger')
                ->setAmountIncVat(19.60)
                ->setVatPercent(25)
                ->setQuantity(100)
                )
                ->addFee(\WebPayItem::invoiceFee()
                    ->setAmountIncVat(29.00)
                    ->setVatPercent(25)
                    ->setName('Svea Invoice Fee')
                )
                ->addDiscount(
                \WebPayItem::fixedDiscount()
                    ->setAmountIncVat(294.00)
                    ->setName('Discount')
                )
        ;
        $total = $order->useInvoicePayment()->getRequestTotals();
        
        $this->assertEquals( 1695.0, $total['total_incvat'] );
        $this->assertEquals( 1356.0, $total['total_exvat'] );
        $this->assertEquals( 339.0, $total['total_vat'] );
    }
}