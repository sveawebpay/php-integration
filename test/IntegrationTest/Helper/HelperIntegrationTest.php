<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class HelperIntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * split mean vat given by shop (i.e. when using a coupon in OpenCart) into two fixedDiscountRows
     */
    public function test_splitMeanToTwoTaxRatesToFormatFixedDiscountRows_TwoRatesInOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                );

        $discountExVatFromShop = 100;
        $meanVatRateFromShop = 18.6667;
        $titleFromShop = "Coupon (1112)";
        $descriptionFromShop = "Value 100";

        $taxRates = Svea\Helper::getTaxRatesInOrder($order);
        $discountRows = Svea\Helper::splitMeanToTwoTaxRates( $discountExVatFromShop, $meanVatRateFromShop, $titleFromShop, $descriptionFromShop, $taxRates );
        foreach($discountRows as $row) {
            $order = $order->addDiscount( $row );
        }


        $formatter = new Svea\WebService\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();

        $newRow = $newRows[2];
        $this->assertEquals("Coupon (1112): Value 100 (25%)", $newRow->Description);
        $this->assertEquals(-66.67, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[3];
        $this->assertEquals("Coupon (1112): Value 100 (6%)", $newRow->Description);
        $this->assertEquals(-33.33, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);
    }

    /**
     * split mean vat given by shop (i.e. when using a coupon in OpenCart) into two fixedDiscountRows
     */
    public function test_splitMeanToTwoTaxRatesToFormatFixedDiscountRows_OneRateInOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                );

        $discountExVatFromShop = 100;
        $meanVatRateFromShop = 25.00;
        $titleFromShop = "Coupon (1112)";
        $descriptionFromShop = "Value 100";

        $taxRates = Svea\Helper::getTaxRatesInOrder($order);
        $discountRows = Svea\Helper::splitMeanToTwoTaxRates( $discountExVatFromShop, $meanVatRateFromShop, $titleFromShop, $descriptionFromShop, $taxRates );
        foreach($discountRows as $row) {
            $order = $order->addDiscount( $row );
        }

        $formatter = new Svea\WebService\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();

        $newRow = $newRows[1];
        $this->assertEquals("Coupon (1112): Value 100", $newRow->Description);
        $this->assertEquals(-100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
    }
}
?>
