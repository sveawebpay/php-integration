<?php

namespace Svea\WebPay\Test\UnitTest\Helper;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;

class HelperTest extends \PHPUnit_Framework_TestCase
{

    // Helper::bround() is an alias for round(x,0,PHP_ROUND_HALF_EVEN)
    function test_bround_RoundsHalfToEven()
    {
        $this->assertEquals(1, Helper::bround(0.51));
        $this->assertEquals(1, Helper::bround(1.49));
        $this->assertEquals(2, Helper::bround(1.5));

        $this->assertEquals(1, Helper::bround(1.49999999999999)); //seems to work with up to 14 decimals, then float creep pushes us over 1.5
        $this->assertEquals(2, Helper::bround(1.500000000000000000000000000000000000000000));
        $this->assertEquals(1, Helper::bround(1.0));
        $this->assertEquals(1, Helper::bround(1));
        //$this->assert( 1, bround("1") );     raise illegalArgumentException??

        $this->assertEquals(4, Helper::bround(4.5));
        $this->assertEquals(6, Helper::bround(5.5));

        $this->assertEquals(-1, Helper::bround(-1.1));
        $this->assertEquals(-2, Helper::bround(-1.5));

        $this->assertEquals(0, Helper::bround(-0.5));
        $this->assertEquals(0, Helper::bround(0));
        $this->assertEquals(0, Helper::bround(0.5));

        $this->assertEquals(262462, Helper::bround(262462.5));

        $this->assertEquals(0.479, Helper::bround(0.4785375, 3));  // i.e. greater than 0.4585, so round up
        $this->assertEquals(0.478, Helper::bround(0.4780000, 3));  // i.e. exactly 0.4585, so round to even
    }

    //--------------------------------------------------------------------------

    function test_splitMeanToTwoTaxRates_returnType()
    {

        $discountAmountIncVat = 100;
        $discountVatAmount = 18.6667;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanToTwoTaxRates($discountAmountIncVat, $discountVatAmount, $discountName, $discountDescription, $allowedTaxRates);

        $this->assertTrue(is_array($discountRows));
        $this->assertTrue(is_a($discountRows[0], 'Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount'));
    }

    function test_splitMeanToTwoTaxRates_splitTwoRates()
    {

        $discountAmountExVat = 100;
        $discountVatAmount = 18.6667;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanToTwoTaxRates($discountAmountExVat, $discountVatAmount, $discountName, $discountDescription, $allowedTaxRates);

        // 200 + 50 (25%)
        // 100 + 6 (6%)
        // -100 => 200/300 @25%, 100/300 @6%
        // => 2/3 * -100 + 2/3 * -25 discount @25%, 1/3 * -100 + 1/3 * -6 discount @6% => -100 @ 18,6667%

        $this->assertEquals(66.67, $discountRows[0]->amountExVat);
        $this->assertEquals(25, $discountRows[0]->vatPercent);
        $this->assertEquals('Coupon(1112)', $discountRows[0]->name);
        $this->assertEquals('-100kr (25%)', $discountRows[0]->description);

        $this->assertEquals(33.33, $discountRows[1]->amountExVat);
        $this->assertEquals(6, $discountRows[1]->vatPercent);
        $this->assertEquals('Coupon(1112)', $discountRows[1]->name);
        $this->assertEquals('-100kr (6%)', $discountRows[1]->description);
    }

    function test_splitMeanToTwoTaxRates_splitTwoRates_2()
    {

        $discountAmountExVat = 100;
        $discountVatAmount = 15.5;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanToTwoTaxRates($discountAmountExVat, $discountVatAmount, $discountName, $discountDescription, $allowedTaxRates);

        // 1000 + 250 (25%)
        // 1000 + 60 (6%)
        // -100 => 1000/2000 @25%, 1000/2000 @6%
        // => 0,5 * -100 + 0,5 * -25 discount @25%, 0,5 * -100 + 0,5 * -6 discount @6%  => -100 @ 15,5%

        $this->assertEquals(50.0, $discountRows[0]->amountExVat);
        $this->assertEquals(25, $discountRows[0]->vatPercent);
        $this->assertEquals('Coupon(1112)', $discountRows[0]->name);
        $this->assertEquals('-100kr (25%)', $discountRows[0]->description);

        $this->assertEquals(50.0, $discountRows[1]->amountExVat);
        $this->assertEquals(6, $discountRows[1]->vatPercent);
        $this->assertEquals('Coupon(1112)', $discountRows[1]->name);
        $this->assertEquals('-100kr (6%)', $discountRows[1]->description);
    }

    // TODO move below from Svea\WebPay\Test\UnitTest\WebService\Helper\WebServiceRowFormatterTest (modified to use Helper::splitMeanToTwoTaxRates) to integrationtest for Helper
    //public function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithDifferentVatRatesPresent2() {

    //--------------------------------------------------------------------------

    function test_getAllTaxRatesInOrder_returnType()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(2)
        );

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertTrue(is_array($taxRates));
    }

    function test_getAllTaxRatesInOrder_getOneRate()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(2)
        );

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertEquals(1, sizeof($taxRates));
        $this->assertEquals(25, $taxRates[0]);
    }

    function test_getAllTaxRatesInOrder_getTwoRates()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setAmountIncVat(125.00)
            ->setQuantity(2)
        )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            );

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertEquals(2, sizeof($taxRates));
        $this->assertEquals(25, $taxRates[0]);
        $this->assertEquals(6, $taxRates[1]);
    }

    function test_getSveaLibraryProperties()
    {
        $libraryPropertiesArray = Helper::getSveaLibraryProperties();
        $this->assertTrue(array_key_exists("library_name", $libraryPropertiesArray));
        $this->assertTrue(array_key_exists("library_version", $libraryPropertiesArray));
    }

    /// new implementation of splitMeanAcrossTaxRates helper method
    //  1u. mean ex to single tax rate: 10e @20% -> 12i @25% 
    //  2u. mean inc to single tax rate: 12i @20% -> 12i @25%
    //  3i. mean inc to single tax rate: 12i @20% -> 12i @25%, priceincvat = true => correct order total at Svea
    //  4i. mean inc to single tax rate: 12i @20% -> 12i @25%, priceincvat = false -> resent as 9.6e @25%, priceincvat = false => correct order total at Svea
    //  5u. mean ex to two tax rates: 8.62e @16% -> 5.67i @25%; 4.33i @6%
    //  6u. mean inc to two tax rate: 10i @16 % -> 5.67i @25%; 4.33i @6%
    //  7i. mean inc to two tax rates: 8.62e @16% -> 5.67i @25%; 4.33i @6%, priceincvat = true => correct order total at Svea
    //  8i. mean inc to two tax rates: 10i @16 % -> 5.67i @25%; 4.33i @6%, priceincvat = false -> resent w/priceincvat = false => correct order total at Svea
    //  9u. mean ex to single tax rate with mean vat rate zero: resend as single row
    //  10u. mean ex to two tax rates with mean vat rate zero: resend as single row

    //  1u. mean ex to single tax rate: 10e @20% -> 12i @25%
    function test_splitMeanAcrossTaxRates_1()
    {
        $discountAmount = 10.0;
        $discountGivenExVat = true;
        $discountMeanVatPercent = 20.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $this->assertEquals(12, $discountRows[0]->amountIncVat);
        $this->assertEquals(25, $discountRows[0]->vatPercent);
        $this->assertEquals('Name', $discountRows[0]->name);
        $this->assertEquals('Description', $discountRows[0]->description);
        $this->assertEquals(null, $discountRows[0]->amountExVat);

        $this->assertEquals(1, count($discountRows));
    }

    //  2u. mean inc to single tax rate: 12i @20% -> 12i @25%
    function test_splitMeanAcrossTaxRates_2()
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

        $this->assertEquals(12, $discountRows[0]->amountIncVat);
        $this->assertEquals(25, $discountRows[0]->vatPercent);
        $this->assertEquals(null, $discountRows[0]->amountExVat);
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

    //  5u. mean ex to two tax rates: 8.62e @16% -> 5.67i @25%; 4.33i @6%
    function test_splitMeanAcrossTaxRates_5()
    {
        $discountAmount = 8.62;
        $discountGivenExVat = true;
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
    }

    //  6u. mean inc to two tax rate: 10i @16 % -> 5.67i @25%; 4.33i @6%
    function test_splitMeanAcrossTaxRates_6()
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

    //  9u. mean ex to single tax rate with mean vat rate zero (exvat): resend as single row w/ zero vat
    function test_splitMeanAcrossTaxRates_9()
    {
        $discountAmount = 10.0;
        $discountGivenExVat = true;
        $discountMeanVatPercent = 0.0;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $this->assertEquals(10.0, $discountRows[0]->amountIncVat);
        $this->assertEquals(0, $discountRows[0]->vatPercent);
        $this->assertEquals('Name', $discountRows[0]->name);
        $this->assertEquals('Description', $discountRows[0]->description);
        $this->assertEquals(null, $discountRows[0]->amountExVat);

        $this->assertEquals(1, count($discountRows));
    }

    //  10u. mean ex to two tax rates with mean vat rate less than zero (incvat): resend as single row w/ zero vat
    function test_splitMeanAcrossTaxRates_10()
    {
        $discountAmount = 10.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = -1;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

        $this->assertEquals(10.0, $discountRows[0]->amountIncVat);
        $this->assertEquals(0, $discountRows[0]->vatPercent);
        $this->assertEquals('Name', $discountRows[0]->name);
        $this->assertEquals('Description', $discountRows[0]->description);
        $this->assertEquals(null, $discountRows[0]->amountExVat);

        $this->assertEquals(1, count($discountRows));
    }

//    function test_splitMeanToTwoTaxRates_splitTwoRates() {
//
//        $discountAmountExVat = 100;
//        $discountVatAmount = 18.6667;
//        $discountName = 'Coupon(1112)';
//        $discountDescription = '-100kr';
//        $allowedTaxRates = array( 25,6 );
//
//        $discountRows = Helper::splitMeanToTwoTaxRates( $discountAmountExVat,$discountVatAmount,$discountName,$discountDescription,$allowedTaxRates );
//
//        // 200 + 50 (25%)
//        // 100 + 6 (6%)
//        // -100 => 200/300 @25%, 100/300 @6%
//        // => 2/3 * -100 + 2/3 * -25 discount @25%, 1/3 * -100 + 1/3 * -6 discount @6% => -100 @ 18,6667%
//
//        $this->assertEquals( 66.67,$discountRows[0]->amountExVat );
//        $this->assertEquals( 25, $discountRows[0]->vatPercent );
//        $this->assertEquals( 'Coupon(1112)', $discountRows[0]->name );
//        $this->assertEquals( '-100kr (25%)', $discountRows[0]->description );
//
//        $this->assertEquals( 33.33,$discountRows[1]->amountExVat );
//        $this->assertEquals( 6, $discountRows[1]->vatPercent );
//        $this->assertEquals( 'Coupon(1112)', $discountRows[1]->name );
//        $this->assertEquals( '-100kr (6%)', $discountRows[1]->description );
//    }


    //  11A. mean inc to two tax rates, 50+6/3 = 18,67% => 19%
    function test_splitMeanAcrossTaxRates_11a()
    {
        $discountAmount = 119.0;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 19;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

//    print_r( $discountRows );

    }

    //  11B. mean inc to two tax rates, 50+6/3 = 18,67%
    function test_splitMeanAcrossTaxRates_11b()
    {
        $discountAmount = 118.67;
        $discountGivenExVat = false;
        $discountMeanVatPercent = 18.67;
        $discountName = 'Name';
        $discountDescription = 'Description';
        $allowedTaxRates = array(25, 6);

        $discountRows = Helper::splitMeanAcrossTaxRates(
            $discountAmount, $discountMeanVatPercent, $discountName, $discountDescription, $allowedTaxRates, $discountGivenExVat
        );

//    print_r( $discountRows );

    }

}
