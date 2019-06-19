<?php

namespace Svea\WebPay\Test\UnitTest\Helper;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebService\WebServiceResponse\PaymentPlanParamsResponse;

class HelperTest extends \PHPUnit\Framework\TestCase
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
    /**
     * @doesNotPerformAssertions
     */
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
    /**
     * @doesNotPerformAssertions
     */
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

    function test_validCardPayCurrency()
    {
        $var = Helper::isCardPayCurrency("SEK");
        $this->assertEquals(true, $var);
    }

    function test_invalidCardPayCurrency()
    {
        $var = Helper::isCardPayCurrency("XXX");
        $this->assertEquals(false, $var);
    }

    function test_validPeppolId()
    {
        $var = Helper::isValidPeppolId("1234:abc12");
        $this->assertEquals(true, $var);
    }

    function test_invalidPeppolId()
    {
        $var = Helper::isValidPeppolId("abcd:1234"); // First 4 characters must be numeric
        $var1 = Helper::isValidPeppolId("1234abc12"); // Fifth character must be ':'.
        $var2 = Helper::isValidPeppolId("1234:ab.c12"); // Rest of the characters must be alphanumeric
        $var3 = Helper::isValidPeppolId("1234:abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12abc12"); // String cannot be longer 55 characters
        $var4 = Helper::isValidPeppolId("1234:"); // String must be longer than 5 characters

        $this->assertEquals(false, $var);
        $this->assertEquals(false, $var1);
        $this->assertEquals(false, $var2);
        $this->assertEquals(false, $var3);
        $this->assertEquals(false, $var4);
    }

    function test_calculateCorrectPricePerMonth()
    {
        $price = 10000;

        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                "CampaignCode" => 213060,
                                "Description" => "Dela upp betalningen på 60 månader",
                                "PaymentPlanType" => "Standard",
                                "ContractLengthInMonths" => 60,
                                "MonthlyAnnuityFactor" => '0.02555',
                                "InitialFee" => '100',
                                "NotificationFee" => '29',
                                "InterestRatePercent" => '16.75',
                                "NumberOfInterestFreeMonths" => 3,
                                "NumberOfPaymentFreeMonths" => 3,
                                "FromAmount" => '1000',
                                "ToAmount" => '50000',
                        ),
                        1 =>
                            (object)array(
                                'CampaignCode' => 222065,
                                'Description' => 'Vårkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        2 =>
                            (object)array(
                                'CampaignCode' => 222066,
                                'Description' => 'Sommarkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        3 =>
                            (object)array(
                                'CampaignCode' => 223060,
                                'Description' => 'Köp nu betala om 3 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '1000',
                                'ToAmount' => '50000',
                            ),
                        4 =>
                            (object)array(
                                'CampaignCode' => 223065,
                                'Description' => 'Black Friday - Cyber Monday',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        5 =>
                            (object)array(
                                'CampaignCode' => 223066,
                                'Description' => 'Julkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        6 =>
                            (object)array(
                                'CampaignCode' => 310012,
                                'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestFree',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.08333',
                                'InitialFee' => '295',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 12,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '30000',
                            ),
                        7 =>
                            (object)array(
                                'CampaignCode' => 410012,
                                'Description' => 'Dela upp betalningen på 12 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.09259',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '19.9',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '100',
                                'ToAmount' => '30000',
                            ),
                        8 =>
                            (object)array(
                                'CampaignCode' => 410024,
                                'Description' => 'Dela upp betalningen på 24 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 24,
                                'MonthlyAnnuityFactor' => '0.04684',
                                'InitialFee' => '350',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '11.5',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '150000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        $arr = Helper::paymentPlanPricePerMonth($price, $params, true);

        $this->assertEquals(287, $arr->values[0]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[1]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[2]['pricePerMonth']);
        $this->assertEquals(10029.0, $arr->values[3]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[4]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[5]['pricePerMonth']);
        $this->assertEquals(894, $arr->values[6]['pricePerMonth']);
        $this->assertEquals(955, $arr->values[7]['pricePerMonth']);
        $this->assertEquals(519, $arr->values[8]['pricePerMonth']);
    }

    function test_calculateCorrectPricePerMonthWithDecimals()
    {
        $price = 10000;

        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                "CampaignCode" => 213060,
                                "Description" => "Dela upp betalningen på 60 månader",
                                "PaymentPlanType" => "Standard",
                                "ContractLengthInMonths" => 60,
                                "MonthlyAnnuityFactor" => '0.02555',
                                "InitialFee" => '100',
                                "NotificationFee" => '29',
                                "InterestRatePercent" => '16.75',
                                "NumberOfInterestFreeMonths" => 3,
                                "NumberOfPaymentFreeMonths" => 3,
                                "FromAmount" => '1000',
                                "ToAmount" => '50000',
                            ),
                        1 =>
                            (object)array(
                                'CampaignCode' => 222065,
                                'Description' => 'Vårkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        2 =>
                            (object)array(
                                'CampaignCode' => 222066,
                                'Description' => 'Sommarkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        3 =>
                            (object)array(
                                'CampaignCode' => 223060,
                                'Description' => 'Köp nu betala om 3 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '1000',
                                'ToAmount' => '50000',
                            ),
                        4 =>
                            (object)array(
                                'CampaignCode' => 223065,
                                'Description' => 'Black Friday - Cyber Monday',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        5 =>
                            (object)array(
                                'CampaignCode' => 223066,
                                'Description' => 'Julkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        6 =>
                            (object)array(
                                'CampaignCode' => 310012,
                                'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestFree',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.08333',
                                'InitialFee' => '295',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 12,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '30000',
                            ),
                        7 =>
                            (object)array(
                                'CampaignCode' => 410012,
                                'Description' => 'Dela upp betalningen på 12 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.09259',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '19.9',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '100',
                                'ToAmount' => '30000',
                            ),
                        8 =>
                            (object)array(
                                'CampaignCode' => 410024,
                                'Description' => 'Dela upp betalningen på 24 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 24,
                                'MonthlyAnnuityFactor' => '0.04684',
                                'InitialFee' => '350',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '11.5',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '150000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        $arr = Helper::paymentPlanPricePerMonth($price, $params, true, 2);

        $this->assertEquals(286.75, $arr->values[0]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[1]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[2]['pricePerMonth']);
        $this->assertEquals(10029.0, $arr->values[3]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[4]['pricePerMonth']);
        $this->assertEquals(10000.0, $arr->values[5]['pricePerMonth']);
        $this->assertEquals(893.58, $arr->values[6]['pricePerMonth']);
        $this->assertEquals(955, $arr->values[7]['pricePerMonth']);
        $this->assertEquals(518.58, $arr->values[8]['pricePerMonth']);
    }
}
