<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\Helper;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\Test\TestUtil;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class HelperIntegrationTest extends \PHPUnit\Framework\TestCase
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
        $this->assertEquals(true, isset($pricesPerMonth->values[0]['pricePerMonth']));
    }

    //  3i. mean inc to single tax rate: 12i @20% -> 12i @25%, priceincvat = true => correct order total at Svea
    function test_splitMeanAcrossTaxRates_3()
    {
        $discountAmount = 12.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 20.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(125.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
            )
            ->addDiscount($discountRows[0])
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(113.00, $response->amount);
    }

    //  4i. mean inc to single tax rate: 12i @20% -> 12i @25%, priceincvat = false -> resent as 9.6e @25%, priceincvat = false => correct order total at Svea
    function test_splitMeanAcrossTaxRates_4()
    {
        $discountAmount = 12.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 20.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(100.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
            )
            ->addDiscount($discountRows[0])
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(113.00, $response->amount);
    }

    //  7i. mean inc to two tax rates: 8.62e @16% -> 5.67i @25%; 4.33i @6%, priceincvat = true => correct order total at Svea
    function test_splitMeanAcrossTaxRates_7()
    {
        $discountAmount = 10.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 16.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $this->assertEquals(5.67, $discountRows[0]->amountIncVat);
        $this->assertEquals(25, $discountRows[0]->vatPercent);
        $this->assertEquals('Name', $discountRows[0]->name);
        $this->assertEquals('Description (25%)', $discountRows[0]->description);
        $this->assertEquals(null, $discountRows[0]->amountExVat);

        $this->assertEquals(4.33, $discountRows[1]->amountIncVat);
        $this->assertEquals(6, $discountRows[1]->vatPercent);
        $this->assertEquals('Name', $discountRows[1]->name);
        $this->assertEquals('Description (6%)', $discountRows[1]->description);
        $this->assertEquals(null, $discountRows[1]->amountExVat);

        $this->assertEquals(2, count($discountRows));

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(125.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
            )
            ->addDiscount($discountRows[0])
            ->addDiscount($discountRows[1])
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(115.00, $response->amount);
    }

    //  8i. mean inc to two tax rates: 10i @16 % -> 5.67i @25%; 4.33i @6%, priceincvat = false -> resent w/priceincvat = false => correct order total at Svea
    function test_splitMeanAcrossTaxRates_8()
    {
        $discountAmount = 10.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 16.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(100.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
            )
            ->addDiscount($discountRows[0])
            ->addDiscount($discountRows[1])
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(115.00, $response->amount);
    }

}

?>
