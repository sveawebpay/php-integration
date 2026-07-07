<?php


namespace Svea\WebPay\WebService\SveaSoap;

/**
 * Abstract class for PaymentPlan, Invoice and AccountCredit for CreatingOrderInformation
 * */
abstract class CreateOrderInformation extends \stdClass
{
    public $CustomerReference;
    public $OrderType;
    public $AddressSelector;
    public $ClientOrderNumber;
    public $OrderRows = array();
    public $CustomerIdentity;
    public $OrderDate;

    public function addOrderRow($orderRow)
    {
        array_push($this->OrderRows['OrderRow'], $orderRow);
    }
}
