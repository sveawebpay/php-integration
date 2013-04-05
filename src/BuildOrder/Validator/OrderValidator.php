<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Description of OrderValidator
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder
 */
abstract class OrderValidator {

    public abstract function validate($order);

    /*
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    protected function validateRequiredFieldsForOrder($order,$errors) {
        if (isset($order->orderRows) == false || count($order->orderRows) == 0) {
            $errors['missing values'] = "OrderRows are required. Use function addOrderRow(Item::orderRow) to get orderrow setters. ";
        }
        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    protected function validateOrderRows($order,$errors) {
        if (isset($order->orderRows)) {
            foreach ($order->orderRows as $row) {
                if (isset($row->quantity) == false) {
                    $errors['missing value'] = "Quantity is required in object Item. Use function Item::setQuantity().";
                }
                if(isset($row->amountExVat) == false && isset($row->vatPercent) == false && isset($row->amountIncVat) == false){
                    $errors['missing values'] = "At least two of the values must be set in object Item::  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().";
                }elseif(isset($row->amountExVat) && (isset($row->vatPercent) == false && isset($row->amountIncVat) == false)){
                    $errors['missing values'] = "At least one of the values must be set in combination with AmountExVat, in object Item:: AmountIncVat or VatPercent for Orderrow. Use functions setAmountIncVat() or setVatPercent().";
                } elseif (isset($row->amountIncVat) && (isset($row->amountExVat) == false) && isset($row->vatPercent) == false) {
                    $errors['missing values'] = "At least one of the values must be set in combination with AmountIncVat, in object Item:: AmountExVat or VatPercent for Orderrow. Use functions setAmountExVat() or setVatPercent().";
                }elseif (isset($row->vatPercent) && (isset($row->amountExVat) == false && isset($row->amountIncVat) == false)) {
                    $errors['missing values'] = "At least one of the values must be set in combination with VatPercent, in object Item:: AmountIncVat or AmountExVat for Orderrow. Use functions setAmountExVat() or setAmountIncVat().";
                }elseif(isset ($row->vatPercent) && (is_numeric ($row->vatPercent) && is_float($row->vatPercent))){
                    $errors['incorrect datatype'] = "Vat must be set as Integer.";

                }

            }
        }
        return $errors;
    }
}