<?php

namespace Svea\WebPay\WebService\SveaSoap;

/**
 * Orderrow
 */
class SveaOrderRow
{
    public $ArticleNumber;
    public $Name;
    public $Description;
    public $PricePerUnit;
    public $NumberOfUnits;
    public $Unit;
    public $TemporaryReference;
    public $VatPercent;
    public $DiscountPercent;
    public $PriceIncludingVat;
}
