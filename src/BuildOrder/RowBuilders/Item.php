<?php

namespace Svea\WebPay\BuildOrder\RowBuilders;

use Svea\WebPay\WebPayItem;

/**
 * Wraps class Svea\WebPay\WebPayItem, while providing backwards compatibility.
 *
 * @deprecated Please use class Svea\WebPay\WebPayItem instead.
 *
 * @author Kristian Grossman-Madsen, anne-hal
 */
class Item
{
    public static function orderRow()
    {
        return WebPayItem::orderRow();
    }

    public static function shippingFee()
    {
        return WebPayItem::shippingFee();
    }

    public static function invoiceFee()
    {
        return WebPayItem::invoiceFee();
    }

    public static function fixedDiscount()
    {
        return WebPayItem::fixedDiscount();
    }

    public static function relativeDiscount()
    {
        return WebPayItem::relativeDiscount();
    }

    public static function individualCustomer()
    {
        return WebPayItem::individualCustomer();
    }

    public static function companyCustomer()
    {
        return WebPayItem::companyCustomer();
    }
}
