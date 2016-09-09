<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\Helper;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class HelperIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * split mean vat given by shop (i.e. when using a coupon in OpenCart) into two fixedDiscountRows
     */
    public function test_splitMeanToTwoTaxRatesToFormatFixedDiscountRows_TwoRatesInOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
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

        $taxRates = Helper::getTaxRatesInOrder($order);
        $discountRows = Helper::splitMeanToTwoTaxRates($discountExVatFromShop, $meanVatRateFromShop, $titleFromShop, $descriptionFromShop, $taxRates);
        foreach ($discountRows as $row) {
            $order = $order->addDiscount($row);
        }


        $formatter = new WebServiceRowFormatter($order);
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
    public function test_splitMeanToTwoTaxRatesToFormatFixedDiscountRows_OneRateInOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
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

        $taxRates = Helper::getTaxRatesInOrder($order);
        $discountRows = Helper::splitMeanToTwoTaxRates($discountExVatFromShop, $meanVatRateFromShop, $titleFromShop, $descriptionFromShop, $taxRates);
        foreach ($discountRows as $row) {
            $order = $order->addDiscount($row);
        }

        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();

        $newRow = $newRows[1];
        $this->assertEquals("Coupon (1112): Value 100", $newRow->Description);
        $this->assertEquals(-100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
    }

    /// Helper::paymentPlanPricePerMonth()
    public function test_paymentPlanPricePerMonth_returns_PaymentPlanPricePerMonth()
    {
        $campaigns =
            WebPay::getPaymentPlanParams(ConfigurationService::getDefaultConfig())
                ->setCountryCode("SE")
                ->doRequest();
        $this->assertTrue($campaigns->accepted);

        $pricesPerMonth = Helper::paymentPlanPricePerMonth(2000, $campaigns, true);
        $this->assertInstanceOf("Svea\WebPay\WebService\GetPaymentPlanParams\PaymentPlanPricePerMonth", $pricesPerMonth);
//        $this->assertEquals(213060, $pricesPerMonth->values[0]['campaignCode']);//don't test to be flexible
        $this->assertEquals(2029, $pricesPerMonth->values[0]['pricePerMonth']);
    }
}

?>
