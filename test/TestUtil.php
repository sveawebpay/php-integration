<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../src/Includes.php';

/**
 * @author Jonas Lith, Kristian Grossman-Madsen
 */
class TestUtil {

    /**
     * Creates an OrderRow object for use in populating order objects.
     * 
     * @return Svea\OrderRow object
     */
    public static function createOrderRow() {
        return WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0);
    }

    /**
     * Creates an OrderRow object using a given tax rate
     * 
     * @param int vatPercent the tax rate for this order row (defaults to 25 if omitted)
     * @return Svea\OrderRow object
     */
    public static function createOrderRowWithVat( $vatPercent = 25 ) {
        return WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent( $vatPercent )
            ->setDiscountPercent(0);
    } 
    
    public static function createHostedOrderRow() {
        return WebPayItem::orderRow()
                ->setAmountExVat(100)
                ->setVatPercent(25)
                ->setQuantity(1);
    }
    
    /**
     * Returns ShippingFeeRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type
     */
    public function buildShippingFee() {
        return function($orderbuilder) {
            return $orderbuilder
              ->addFee(
                WebPayItem::shippingFee()
                     ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                );
        };
    }

    /**
     * Returns InvoicefeeRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type
     */
    public function buildInvoiceFee() {
        return function($orderbuilder) {
            return $orderbuilder
                ->addFee(
                WebPayItem::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        );
        };
    }

    /**
     * Returns orderrow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return OrderBuilder

      function buildRow() {
      return function($orderBuilder) {
      return $orderBuilder
      ->beginOrderRow()
      ->setArticleNumber(1)
      ->setQuantity(2)
      ->setAmountExVat(100.00)
      ->setDesc("Specification")
      ->setName('Prod')
      ->setUnit("st")
      ->setVatPercent(25)
      ->setDiscountPercent(0)
      ->endOrderRow();
      };
      }
     *
     */
    /**
     *  Returns FixedDiscountRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildFixedDiscountRow() {
      return function ($orderbuilder) {
      return $orderbuilder
      ->beginFixedDiscount()
      ->setDiscountId("1")
      ->setAmount(100.00)
      ->setUnit("st")
      ->setDesc("FixedDiscount")
      ->setName("Fixed")
      ->endFixedDiscount(0);
      };
      }
     *
     */
    /**
     *  Returns RelativeDiscountRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildRelativeDiscountRow() {
      return function($orderbuilder) {
      return $orderbuilder
      ->beginRelativeDiscount()
      ->setDiscountId("1")
      ->setDiscountPercent(50)
      ->setUnit("st")
      ->setName('Relative')
      ->setDesc("RelativeDiscount")
      ->endRelativeDiscount();
      };
      }
     */

    /**
     *  Returns CustomerIdentity to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildCustomerIdentity() {
      return function ($orderbuilder) {
      return $orderbuilder
      ->beginIndividualCustomerIdentity()
      ->setNationalIdNumber(194605092222)
      ->setInitials("SB")
      ->setBirthDate(1923, 12, 12)
      ->setName("Tess", "Testson")
      ->setEmail("test@svea.com")
      ->setPhoneNumber(999999)
      ->setIpAddress("123.123.123")
      ->setStreetAddress("Gatan",23)
      ->setCoAddress("c/o Eriksson")
      ->setZipCode(9999)
      ->setLocality("Stan")
      ->endIndividualCustomerIdentity();
      };
      }
     */

}
