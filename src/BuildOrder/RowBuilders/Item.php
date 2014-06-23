<?php
// Item class is not included in Svea namespace, is wrapper for WebPayItem

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Wraps class WebPayItem, while providing backwards compatibility.
 * 
 * @deprecated Please use class WebPayItem instead.
 * 
 * @author Kristian Grossman-Madsen, anne-hal
 */
class Item {

     public static function orderRow() {
         return WebPayItem::orderRow();
    }

    public static function shippingFee() {
        return WebPayItem::shippingFee();
    }

    public static function invoiceFee() {
        return WebPayItem::invoiceFee();
    }

    public static function fixedDiscount() {
        return WebPayItem::fixedDiscount();
    }

    public static function relativeDiscount() {
        return WebPayItem::relativeDiscount();
    }

    public static function individualCustomer() {
        return WebPayItem::individualCustomer();
    }

    public static function companyCustomer() {
        return WebPayItem::companyCustomer();
    }
}
