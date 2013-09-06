<?php
// WebPayItem class is not included in Svea namespace 

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Supercedes class Item, while providing the same functionality.
 * WebPayItem is external to Svea namespace along with class WebPay.
 *  
 * @author Kristian Grossman-Madsen
 */
class WebPayItem {

     public static function orderRow() {
         return new Svea\OrderRow();
    }

    public static function shippingFee() {
        return new Svea\ShippingFee();
    }

    public static function invoiceFee() {
        return new Svea\InvoiceFee();
    }

    public static function fixedDiscount() {
        return new Svea\FixedDiscount();
    }

    public static function relativeDiscount() {
        return new Svea\RelativeDiscount();
    }

    public static function individualCustomer() {
        return new Svea\IndividualCustomer();
    }

    public static function companyCustomer() {
        return new Svea\CompanyCustomer();
    }
}
