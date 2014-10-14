<?php
// WebPayItem class is not included in Svea namespace 

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * The WebPayItem class provides entrypoint methods to the different row items 
 * that make up an order, as well as the customer identity information items.
 * 
 * @api
 * @author Kristian Grossman-Madsen
 */
class WebPayItem {

    /**
     * The WebPayItem::orderRow() entrypoint method is used to specify order items like products and services. 
     * It is required to have a minimum of one order row in an order.
     * 
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate: 
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     * 
     * If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
     * quantities of an item, as the package bases the total order sum on a calculated price ex. vat.
     *  
     *      $orderrow = WebPayItem::orderRow()
     *          ->setAmountExVat(100.00)        // optional, recommended, use precisely two of the price specification methods
     *          ->setVatPercent(25)             // optional, recommended, use precisely two of the price specification methods
     *          ->setAmountIncVat(125.00)       // optional, use precisely two of the price specification methods
     *          ->setQuantity(2)                // required
     *          ->setUnit("pcs.")               // optional
     *          ->setName('name')               // optional, invoice & payment plan orders will merge "name" with "description" 
     *          ->setDescription("description") // optional, invoice & payment plan orders will merge "name" with "description" 
     *          ->setArticleNumber("1")         // optional
     *          ->setDiscountPercent(0)         // optional
     *      );
     * 
     * @return \Svea\OrderRow
     */
    public static function orderRow() {
         return new Svea\OrderRow();
    }

    /**
     * Use this only when supplying NumberedOrderRow items for the various WebPayAdmin order row administration functions.
     * @return \Svea\NumberedOrderRow
     */
     public static function numberedOrderRow() {
         return new Svea\NumberedOrderRow();
    }
       
    /**
     * The WebPayItem::shippingFee() entrypoint method is used to specify order shipping fee rows.
     * It is not required to have a shipping fee row in an order.
     * 
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate: 
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     * 
     *      $shippingFee = WebPayItem::shippingFee()
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
     * @return \Svea\ShippingFee
     */
    public static function shippingFee() {
        return new Svea\ShippingFee();
    }

    /**
     * The WebPayItem::invoiceFee() entrypoint method is used to specify fees associated with a payment method (i.e. invoice fee).
     * It is not required to have an invoice fee row in an order.
     * 
     * Specify the item price using precisely two of these methods in order to specify the item price and tax rate: 
     * setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().
     * 
     *      $invoiceFee = WebPayItem::invoiceFee()
     *          ->setAmountExVat(100.00)        // optional, recommended, use precisely two of the price specification methods
     *          ->setVatPercent(25)             // optional, recommended, use precisely two of the price specification methods
     *          ->setAmountIncVat(125.00)       // optional, use precisely two of the price specification methods
     *          ->setUnit("pcs.")               // optional
     *          ->setName('name')               // optional
     *          ->setDescription("description") // optional
     *          ->setDiscountPercent(0)         // optional
     *      );
     * 
     * @return \Svea\InvoiceFee
     */    
    public static function invoiceFee() {
        return new Svea\InvoiceFee();
    }

    /**
     * @return \Svea\FixedDiscount
     */
    public static function fixedDiscount() {
        return new Svea\FixedDiscount();
    }

    /**
     * @return \Svea\RelativeDiscount
     */
    public static function relativeDiscount() {
        return new Svea\RelativeDiscount();
    }

    /**
     * @return \Svea\IndividualCustomer
     */
    public static function individualCustomer() {
        return new Svea\IndividualCustomer();
    }

    /**
     * @return \Svea\CompanyCustomer
     */
    public static function companyCustomer() {
        return new Svea\CompanyCustomer();
    }
}
