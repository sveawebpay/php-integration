<?php
namespace Svea;

$root = realpath(dirname(__FILE__) );
require_once $root . '/../../../src/Includes.php';

class HelperTest extends \PHPUnit_Framework_TestCase {

    // TODO check out parameterised tests
    function test_bround_RoundsHalfToEven() {
        $this->assertEquals( 1, Helper::bround(0.51) );
        $this->assertEquals( 1, Helper::bround(1.49) );
        $this->assertEquals( 2, Helper::bround(1.5) );

        $this->assertEquals( 1, Helper::bround(1.49999) ); //seems to work with up to 5 decimals, then float creep pushes us over 1.5
        $this->assertEquals( 2, Helper::bround(1.500000000000000000000000000000000000000000) );
        $this->assertEquals( 1, Helper::bround(1.0) );
        $this->assertEquals( 1, Helper::bround(1) );
        //$this->assert( 1, bround("1") );     raise illegalArgumentException??

        $this->assertEquals( 4, Helper::bround(4.5) );
        $this->assertEquals( 6, Helper::bround(5.5) );

        $this->assertEquals( -1, Helper::bround(-1.1) );
        $this->assertEquals( -2, Helper::bround(-1.5) );

        $this->assertEquals( 0, Helper::bround(-0.5) );
        $this->assertEquals( 0, Helper::bround(0) );
        $this->assertEquals( 0, Helper::bround(0.5) );

        $this->assertEquals( 262462, Helper::bround(262462.5) );

        $this->assertEquals( 0.479, Helper::bround(0.4785375,3) );  // i.e. greater than 0.4585, so round up
        $this->assertEquals( 0.478, Helper::bround(0.4780000,3) );  // i.e. exactly 0.4585, so round to even
    }

    //--------------------------------------------------------------------------

    function test_splitMeanToTwoTaxRates_returnType() {

        $discountAmountIncVat = 100;
        $discountVatAmount = 18.6667;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array( 25,6 );

        $discountRows = Helper::splitMeanToTwoTaxRates( $discountAmountIncVat,$discountVatAmount,$discountName,$discountDescription,$allowedTaxRates );

        $this->assertTrue( is_array($discountRows) );
        $this->assertTrue( is_a( $discountRows[0], 'Svea\FixedDiscount' ) );
    }
    function test_splitMeanToTwoTaxRates_splitTwoRates() {

        $discountAmountExVat = 100;
        $discountVatAmount = 18.6667;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array( 25,6 );

        $discountRows = Helper::splitMeanToTwoTaxRates( $discountAmountExVat,$discountVatAmount,$discountName,$discountDescription,$allowedTaxRates );

        // 200 + 50 (25%)
        // 100 + 6 (6%)
        // -100 => 200/300 @25%, 100/300 @6%
        // => 2/3 * -100 + 2/3 * -25 discount @25%, 1/3 * -100 + 1/3 * -6 discount @6% => -100 @ 18,6667%

        $this->assertEquals( 66.67,$discountRows[0]->amountExVat );
        $this->assertEquals( 25, $discountRows[0]->vatPercent );
        $this->assertEquals( 'Coupon(1112)', $discountRows[0]->name );
        $this->assertEquals( '-100kr (25%)', $discountRows[0]->description );

        $this->assertEquals( 33.33,$discountRows[1]->amountExVat );
        $this->assertEquals( 6, $discountRows[1]->vatPercent );
        $this->assertEquals( 'Coupon(1112)', $discountRows[1]->name );
        $this->assertEquals( '-100kr (6%)', $discountRows[1]->description );
    }

    function test_splitMeanToTwoTaxRates_splitTwoRates_2() {

        $discountAmountExVat = 100;
        $discountVatAmount = 15.5;
        $discountName = 'Coupon(1112)';
        $discountDescription = '-100kr';
        $allowedTaxRates = array( 25,6 );

        $discountRows = Helper::splitMeanToTwoTaxRates( $discountAmountExVat,$discountVatAmount,$discountName,$discountDescription,$allowedTaxRates );

        // 1000 + 250 (25%)
        // 1000 + 60 (6%)
        // -100 => 1000/2000 @25%, 1000/2000 @6%
        // => 0,5 * -100 + 0,5 * -25 discount @25%, 0,5 * -100 + 0,5 * -6 discount @6%  => -100 @ 15,5%

        $this->assertEquals( 50.0,$discountRows[0]->amountExVat );
        $this->assertEquals( 25, $discountRows[0]->vatPercent );
        $this->assertEquals( 'Coupon(1112)', $discountRows[0]->name );
        $this->assertEquals( '-100kr (25%)', $discountRows[0]->description );

        $this->assertEquals( 50.0,$discountRows[1]->amountExVat );
        $this->assertEquals( 6, $discountRows[1]->vatPercent );
        $this->assertEquals( 'Coupon(1112)', $discountRows[1]->name );
        $this->assertEquals( '-100kr (6%)', $discountRows[1]->description );
    }

    // TODO move below from WebServiceRowFormatterTest (modified to use Helper::splitMeanToTwoTaxRates) to integrationtest for Helper
    //public function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithDifferentVatRatesPresent2() {

    //--------------------------------------------------------------------------

    function test_getAllTaxRatesInOrder_returnType() {
        $config = SveaConfig::getDefaultConfig();
        $order = \WebPay::createOrder($config);
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
        ;

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertTrue( is_array($taxRates) );
    }

    function test_getAllTaxRatesInOrder_getOneRate() {
        $config = SveaConfig::getDefaultConfig();
        $order = \WebPay::createOrder($config);
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
        ;

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertEquals( 1, sizeof($taxRates) );
        $this->assertEquals( 25, $taxRates[0] );
    }

    function test_getAllTaxRatesInOrder_getTwoRates() {
        $config = SveaConfig::getDefaultConfig();
        $order = \WebPay::createOrder($config);
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setAmountIncVat(125.00)
                ->setQuantity(2)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
        ;

        $taxRates = Helper::getTaxRatesInOrder($order);

        $this->assertEquals( 2, sizeof($taxRates) );
        $this->assertEquals( 25, $taxRates[0] );
        $this->assertEquals( 6, $taxRates[1] );
    }


}
?>
