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
            $errors['missing values'] = "OrderRows are required. Use function beginOrderRow() to get orderrow setters. End with endOrderRow().";
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
                    $errors['missing value'] = "Quantity is required. Use function setQuantity().";
                }
                if(isset($row->amountExVat) == false && isset($row->vatPercent) == false && isset($row->amountIncVat) == false){
                    $errors['missing values'] = "At least two of the values must be set:  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent() after using function beginOrderRow().";
                }elseif(isset($row->amountExVat) && (isset($row->vatPercent) == false && isset($row->amountIncVat) == false)){
                    $errors['missing values'] = "At least one of the values must be set in combination with AmountExVat: AmountIncVat or VatPercent for Orderrow. Use functions setAmountIncVat() or setVatPercent() after using function beginOrderRow().";
                } elseif (isset($row->amountIncVat) && (isset($row->amountExVat) == false) && isset($row->vatPercent) == false) {
                    $errors['missing values'] = "At least one of the values must be set in combination with AmountIncVat: AmountExVat or VatPercent for Orderrow. Use functions setAmountExVat() or setVatPercent() after using function beginOrderRow().";
                }elseif (isset($row->vatPercent) && (isset($row->amountExVat) == false && isset($row->amountIncVat) == false)) {
                    $errors['missing values'] = "At least one of the values must be set in combination with VatPercent: AmountIncVat or AmountExVat for Orderrow. Use functions setAmountExVat() or setAmountIncVat() after using function beginOrderRow().";
                }
                
            }
        }
        return $errors;
    }
}

?>
