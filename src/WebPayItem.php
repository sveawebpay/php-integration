<?php
// Svea\WebPay\WebPayItem class is not included in Svea namespace

namespace Svea\WebPay;
use Svea\WebPay\BuildOrder\RowBuilders\CompanyCustomer;
use Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount;
use Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer;
use Svea\WebPay\BuildOrder\RowBuilders\InvoiceFee;
use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\BuildOrder\RowBuilders\OrderRow;
use Svea\WebPay\BuildOrder\RowBuilders\RelativeDiscount;
use Svea\WebPay\BuildOrder\RowBuilders\ShippingFee;
use Svea\WebPay\Checkout\Model\PresetValue;

/**
 * The Svea\WebPay\WebPayItem class provides entrypoint methods to the different row items
 * that make up an order, as well as the customer identity information items.
 *
 * The Svea\WebPay\WebPayItem class provides entrypoint methods to the different row items that make up an order, as well as the customer identity information items.
 *
 * An order must contain one or more order rows. You may add invoice fees, shipping fees and discounts to an order.
 *
 * Note that while it is possible to add multiples of fee and discount rows, the package will group rows according to type before sending them to Svea:
 *
 *  1. all order rows, in the order they were added using addOrderRow()
 *  2. any shipping fee rows, in the order they were added using addShippingFee()
 *  3. any invoice fee rows, in the order they were added using addShippingFee()
 *  4. any fixed discount rows, in the order they were added using addFixedDiscount()
 *  5. any relative discount rows, in the order they were added using addRelativeDiscount()
 *
 * Also, for relative discounts, or fixed discounts specified using only setAmountIncVat() or only setAmountExVat() there may be several discount rows
 * added, should the order include more than one different vat rate. It is not recommended to specify more than one relative discount row per order, or
 * more than one fixed discount specified using only setAmountIncVat() or only setAmountExVat().
 *
 * @author Kristian Grossman-Madsen
 */
class WebPayItem
{

    /**
     * The Svea\WebPay\WebPayItem::orderRow() entrypoint method is used to specify order items like products and services.
     * It is required to have a minimum of one order row in an order.
     *
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     *
     * If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
     * quantities of an item, as the package bases the total order sum on a calculated price ex. vat.
     *
     *      $orderrow = Svea\WebPay\WebPayItem::orderRow()
     *          ->setAmountExVat(100.00)        // optional, recommended, use precisely two of the price specification methods
     *          ->setVatPercent(25)             // optional, recommended, use precisely two of the price specification methods
     *          ->setAmountIncVat(125.00)       // optional, use precisely two of the price specification methods
     *          ->setQuantity(2)                // required
     *          ->setUnit("pcs.")               // optional
     *          ->setName('name')               // optional, invoice, accountCredit & payment plan orders will merge "name" with "description"
     *          ->setDescription("description") // optional, invoice, accountCredit & payment plan orders will merge "name" with "description"
     *          ->setArticleNumber("1")         // optional
     *          ->setDiscountPercent(0)         // optional
     *      );
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\OrderRow
     */
    public static function orderRow()
    {
        return new OrderRow();
    }

    /**
     * Use this only when supplying NumberedOrderRow items for the various Svea\WebPay\WebPayAdmin order row administration functions.
     * @return \Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow
     */
    public static function numberedOrderRow()
    {
        return new NumberedOrderRow();
    }

    /**
     * The Svea\WebPay\WebPayItem::shippingFee() entrypoint method is used to specify order shipping fee rows.
     * It is not required to have a shipping fee row in an order.
     *
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     *
     *      $shippingFee = Svea\WebPay\WebPayItem::shippingFee()
     *          ->setAmountExVat(100.00)        // optional, recommended, use precisely two of the price specification methods
     *          ->setVatPercent(25)             // optional, recommended, use precisely two of the price specification methods
     *          ->setAmountIncVat(125.00)       // optional, use precisely two of the price specification methods
     *          ->setUnit("pcs.")               // optional
     *          ->setName('name')               // optional
     *          ->setDescription("description") // optional
     *          ->setShippingId('33')           // optional
     *          ->setDiscountPercent(0)         // optional
     *      );
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\ShippingFee
     */
    public static function shippingFee()
    {
        return new ShippingFee();
    }

    /**
     * The Svea\WebPay\WebPayItem::invoiceFee() entrypoint method is used to specify fees associated with a payment method (i.e. invoice fee).
     * It is not required to have an invoice fee row in an order.
     *
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     *
     *      $invoiceFee = Svea\WebPay\WebPayItem::invoiceFee()
     *          ->setAmountExVat(100.00)        // optional, recommended, use precisely two of the price specification methods
     *          ->setVatPercent(25)             // optional, recommended, use precisely two of the price specification methods
     *          ->setAmountIncVat(125.00)       // optional, use precisely two of the price specification methods
     *          ->setUnit("pcs.")               // optional
     *          ->setName('name')               // optional
     *          ->setDescription("description") // optional
     *          ->setDiscountPercent(0)         // optional
     *      );
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\InvoiceFee
     */
    public static function invoiceFee()
    {
        return new InvoiceFee();
    }

    /**
     * Use Svea\WebPay\WebPayItem::fixedDiscount() when the discount or coupon is expressed as a fixed discount amount.
     *
     * If no vat rate is given, we calculate the discount split across the order row vat rates present in the order.
     * This will ensure that the correct discount vat is applied to the order.
     *
     * If there are several vat rates present in the order, the discount will be split proportionally across the order row vat
     * rates. For examples, including the resulting discount rows, see the test suite file UnitTest/InvoicePaymentTest.php.
     *
     * Otherwise, it is required to use at least two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
     * If two of these three attributes are specified, we honour the amount indicated and the given discount tax rate.
     *
     *      $fixedDiscount = Svea\WebPay\WebPayItem::fixedDiscount()
     *          ->setAmountIncVat(100.00)               // recommended, see info above
     *          ->setAmountExVat(1.0)                   // optional, see info above
     *          ->setVatPercent(25)                     // optional, see info above
     *          ->setDiscountId("1")                    // optional
     *          ->setUnit("st")                         // optional
     *          ->setName("Fixed")                      // optional
     *          ->setDescription("FixedDiscount")       // optional
     *      );
     *
     * @see \Svea\FixedDiscount
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount
     */
    public static function fixedDiscount()
    {
        return new FixedDiscount();
    }

    /**
     * Use Svea\WebPay\WebPayItem::relativeDiscount() when the discount or coupon is expressed as a percentage of the total product amount.
     *
     * The discount will be calculated based on the total sum of all order rows specified using addOrderRow(), it does not
     * apply to invoice or shipping fees.
     *
     * If there are several vat rates present in the order, the discount will be split proportionally across the order row vat
     * rates. For examples, including the resulting discount rows, see the test suite file UnitTest/InvoicePaymentTest.php.
     *
     * Specify the discount using RelativeDiscount methods:
     *
     *      $relativeDiscount = Svea\WebPay\WebPayItem::relativeDiscount()
     *          ->setDiscountPercent(10.0)          // required,
     *          ->setDiscountId("1")                // optional
     *          ->setUnit("st.")                    // optional
     *          ->setName("DiscountName")           // optional
     *          ->setDescription("DiscountDesc.")   // optional
     *      );
     *
     * @see \Svea\RelativeDiscount
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\RelativeDiscount
     */
    public static function relativeDiscount()
    {
        return new RelativeDiscount();
    }


    /**
     * Use Svea\WebPay\WebPayItem::individualCustomer() to add individual customer information to an order.
     *
     * Note that "required" below as a requirement only when using the invoice or payment plan
     * payment methods, and that the required attributes vary etween countries.
     *
     * (For card and direct bank orders, adding customer information to the order is optional,
     * unless you're using getPaymentUrl() to set up a prepared payment.)
     *
     *  IndividualCustomer individual = Svea\WebPay\WebPayItem::individualCustomer()
     *      ->setNationalIdNumber() // Numeric    // invoice, accountCredit, paymentplan: required for customers in SE, NO, DK, FI
     *      ->setName()             // String    // invoice, accountCredit, paymentplan: required, use (firstname, lastname) for customers in NL and DE
     *      ->setBirthDate()        // Numeric    // invoice, accountCredit, paymentplan: required for individual customers in NL and DE
     *      ->setInitials()         // String    // invoice, accountCredit, paymentplan: required for individual customers in NL
     *      ->setCoAddress()        // String    // invoice, accountCredit, paymentplan: optional
     *      ->setStreetAddress()    // String    // invoice, accountCredit, paymentplan: required, use (street, housenumber) in NL and DE
     *      ->setZipCode)           // String    // invoice, accountCredit, paymentplan: required in NL and DE
     *      ->setLocality()         // String    // invoice, accountCredit, paymentplan: required in NL and DE
     *      ->setPhoneNumber()      // String    // invoice, accountCredit, paymentplan: optional but desirable
     *      ->setEmail()            // String    // invoice, accountCredit, paymentplan: optional but desirable
     *      ->setIpAddress()        // String    // invoice, accountCredit, paymentplan: optional but desirable; card: required for getPaymentUrl() orders only
     *  ;
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer
     */
    public static function individualCustomer()
    {
        return new IndividualCustomer();
    }

    /**
     * Use Svea\WebPay\WebPayItem::companyCustomer() to add individual customer information to an order.
     *
     * Note that "required" below as a requirement only when using the invoice or payment plan
     * payment methods, and that the required attributes vary etween countries.
     *
     * (For card and direct bank orders, adding customer information to the order is optional,
     * unless you're using getPaymentUrl() to set up a prepared payment.)
     *
     *  CompanyCustomer company = Svea\WebPay\WebPayItem::companyCustomer()
     *      ->setNationalIdNumber() // Numeric    // invoice: required for customers in SE, NO, DK, FI
     *      ->setCompanyName()      // String    // invoice: required (companyname) for company customers in NL and DE
     *      ->setVatNumber()        // Numeric    // invoice: required for individual customers in NL and DE
     *      ->setCoAddress()        // String    // invoice: optional
     *      ->setStreetAddress()    // String    // invoice: required, use (street, housenumber) in NL and DE
     *      ->setZipCode)           // String    // invoice: required in NL and DE
     *      ->setLocality()         // String    // invoice: required in NL and DE
     *      ->setPhoneNumber()      // String    // invoice: optional but desirable
     *      ->setEmail()            // String    // invoice: optional but desirable
     *      ->setIpAddress()        // String    // invoice: optional but desirable; card: required for getPaymentUrl() orders only
     *      ->setAddressSelector()  // String    // invoice: optional but recommended; received from Svea\WebPay\WebPay::getAddresses() request response
     *  ;
     *
     * @return \Svea\WebPay\BuildOrder\RowBuilders\CompanyCustomer
     */
    public static function companyCustomer()
    {
        return new CompanyCustomer();
    }

    /**
     * Use Svea\WebPay\WebPayItem::presetValue() to add individual preset value information to an order
     *
     * These information are important for creating order via checkout method
     *
     * $presetValue = new Svea\WebPay\WebPay::presetValue()
     *      ->setValueAs
     *
     * @return \Svea\WebPay\Checkout\Model\PresetValue
     */
    public static function presetValue()
    {
        return new PresetValue();
    }
}
