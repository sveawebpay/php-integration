<?php

include_once SVEA_REQUEST_DIR . "/Includes.php";
/**
 * Description of Item
 *
 * @author anne-hal
 */
class Item {

     public static function orderRow(){
         return new OrderRow();
    }

    public static function shippingFee(){
        return new ShippingFee();
    }

    public static function invoiceFee(){
        return new InvoiceFee();
    }

    public static function fixedDiscount(){
        return new FixedDiscount();
    }

    public static function relativeDiscount(){
        return new RelativeDiscount();
    }

    public static function individualCustomer(){
        return new IndividualCustomer();
    }

    public static function companyCustomer(){
        return new CompanyCustomer();
    }
}