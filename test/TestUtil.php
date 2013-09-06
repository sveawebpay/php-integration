<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../src/Includes.php';

/**
 * @author Jonas Lith
 */
class TestUtil {
    
    public static function createOrderRow() {
        return \WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0);
    }
    
    public static function createHostedOrderRow() {
        return \WebPayItem::orderRow()
                ->setAmountExVat(100)
                ->setVatPercent(25)
                ->setQuantity(1);
    }
}
