<?php

namespace Svea\WebPay\BuildOrder\Validator;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\ExVatRestrictionValidator;

/**
 * OrderValidator is called by prepareRequest and doRequest methods. It checks
 * that all required fields are present and of the correct type. Errors found
 * are returned as associative type-message pairs in an error array.
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen, Fredrik Sundell for Svea Webpay
 */
abstract class OrderValidator
{
    public abstract function validate($order);

    /**
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    public function validatePeppolId($order, $errors)
    {
        if (isset($order->peppolId)) {

            if($this->isCompany == false)
            {
                $errors['incorrect value'] = "CustomerType must be a company when using PeppolId.";
            }

            if(is_numeric(substr($order->peppolId,0,4)) == false )
            {
                $errors['incorrect value'] = "First 4 characters of PeppolId must be numeric.";
            }

            if(ctype_alnum(substr($order->peppolId,6)) == false)
            {
                $errors['incorrect value'] = "All characters after the fifth character in PeppolId must be alphanumeric.";
            }

            if(substr($order->peppolId,4,1) != ":")
            {
                $errors['incorrect value'] = "The fifth character of PeppolId must be \":\".";
            }

            if(strlen($order->peppolId) > 55)
            {
                $errors['incorrect value'] = "PeppolId is too long, must be 55 characters or fewer.";
            }

            if(strlen($order->peppolId) < 6)
            {
                $errors['incorrect value'] = "PeppolId is too short, must be 6 characters or longer.";
            }
        }

        return $errors;
    }

    /*
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    protected function validateRequiredFieldsForOrder($order, $errors)
    {
        if (isset($order->orderRows) == false || count($order->orderRows) == 0) {
            $errors['missing values'] = 'OrderRows are required. Use function addOrderRow(Svea\WebPay\WebPayItem::orderRow) to get orderrow setters. ';
        }

        return $errors;
    }

    /**
     * @param $order
     * @param $errors
     * @return array $errors
     */
    protected function validateOrderRows($order, $errors)
    {
        if (isset($order->orderRows)) {
            foreach ($order->orderRows as $row) {
                if (isset($row->quantity) == false) {
                    $errors['missing value'] = "Quantity is required in object Svea\WebPay\BuildOrder\RowBuilders\Item. Use function Svea\WebPay\BuildOrder\RowBuilders\Item::setQuantity().";
                }

                // Precisely two of the following attributes must be set in order to calculate total amount and vat
                if ((isset($row->amountExVat) == false && isset($row->vatPercent) == false && isset($row->amountIncVat) == false) ||
                    (isset($row->amountExVat) == true && isset($row->vatPercent) == true && isset($row->amountIncVat) == true)
                ) {
                    $errors['missing values'] = "Precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().";
                } elseif (isset($row->amountExVat) && (isset($row->vatPercent) == false && isset($row->amountIncVat) == false)) {
                    $errors['missing values'] = "At least one of these values must be set in combination with AmountExVat, in object Svea\WebPay\BuildOrder\RowBuilders\Item:: AmountIncVat or VatPercent for Orderrow. Use functions setAmountIncVat() or setVatPercent().";
                } elseif (isset($row->amountIncVat) && (isset($row->amountExVat) == false) && isset($row->vatPercent) == false) {
                    $errors['missing values'] = "At least one of the values must be set in combination with AmountIncVat, in object Svea\WebPay\BuildOrder\RowBuilders\Item:: AmountExVat or VatPercent for Orderrow. Use functions setAmountExVat() or setVatPercent().";
                } elseif (isset($row->vatPercent) && (isset($row->amountExVat) == false && isset($row->amountIncVat) == false)) {
                    $errors['missing values'] = "At least one of the values must be set in combination with VatPercent, in object Svea\WebPay\BuildOrder\RowBuilders\Item:: AmountIncVat or AmountExVat for Orderrow. Use functions setAmountExVat() or setAmountIncVat().";
                }

                // force correct order type of present attributes, see class OrderRow 
                if (isset($row->vatDiscount) && !(is_int($row->vatDiscount) || is_float($row->vatDiscount)))
                    $errors['incorrect datatype'] = "vatDiscount is not of type int or float.";

                if (isset($row->articleNumber) && !is_string($row->articleNumber))
                    $errors['incorrect datatype'] = 'articleNumber is not of type string.';

                if (isset($row->quantity) && !(is_numeric($row->quantity)))
                    $errors['incorrect datatype'] = "quantity is not numeric.";

                if (isset($row->unit) && !is_string($row->unit))
                    $errors['incorrect datatype'] = 'unit is not of type string.';

                if (isset($row->amountExVat) && !(is_int($row->amountExVat) || is_float($row->amountExVat)))
                    $errors['incorrect datatype'] = 'amountExVat is not of type float or int.';

                if (isset($row->amountIncVat) && !(is_int($row->amountIncVat) || is_float($row->amountIncVat)))
                    $errors['incorrect datatype'] = 'amountIncVat is not of type float or int.';

                if (isset($row->name) && !is_string($row->name))
                    $errors['incorrect datatype'] = 'name is not of type string.';

                if (isset($row->description) && !is_string($row->description))
                    $errors['incorrect datatype'] = 'description is not of type string.';

                if (isset($row->vatPercent) && !(is_int($row->vatPercent) || is_float($row->vatPercent)))
                    $errors['incorrect datatype'] = "vatPercent is not of type int.";

                if (isset($row->discountPercent) && !(is_int($row->discountPercent) || is_float($row->discountPercent)))
                    $errors['incorrect datatype'] = "discountPercent is not of type int.";
            }
        }

        return $errors;
    }

    /**
    * @param $order
    * @param $errors
    * @return mixed
    *
    * Validate all rows - products and shipping and discount or voucher rows
    * Because that we use rows instead of orderRows
    *
    */
    protected function validateCheckoutOrderRows($order, $errors)
    {
        /**
         * * @var \Svea\WebPay\BuildOrder\RowBuilders\OrderRow [] $rows
         */
        $orderRows = $order->rows;

        if (count($orderRows) === 0) {
            $orderRows = $order->orderRows;
        }

        foreach ($orderRows as $row) {
            if (empty($row->name)) {
                $errors['missing values'] = "Name must be set in the Svea\\WebPay\\WebPayItem object. Use functions setName().";
            }
        }

        return $errors;
    }


    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return array
     */
    protected function validateOrderId(CheckoutOrderBuilder $order, $errors)
{
    if (!is_numeric($order->getId()) || !is_int($order->getId())) {
        $errors['incorrect OrderId'] = "orderId can't be empty and must be int";
    }

    return $errors;
}

    /**
     * @param CheckoutOrderBuilder $request
     * @param array $errors
     * @return array
     */
    protected function validatePresetIsCompanyIsBoolean($request, $errors)
    {
        if (count($request->getPresetValues()) > 0) {
            foreach ($request->getPresetValues() as $presetValue) {
                if(strtolower($presetValue->getTypeName()) == 'iscompany')
                {
                    if(!is_bool($presetValue->getValue()))
                    {
                        $errors['incorrect type'] = "isCompany must be of type boolean";
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $request
     * @param array $errors
     * @return array
     */
    protected function validatePresetIsCompanyIsSet($request, $errors)
    {
        if(count($request->getPresetValues()) == 0)
        {
            $errors['missing value'] = "isCompany presetValue is not set";
        }
        else
        {
            foreach ($request->getPresetValues() as $presetValue) {
                if (strtolower($presetValue->getTypeName()) == 'iscompany') {
                    if ($presetValue->getValue() === null) {
                        $errors['missing value'] = "isCompany value is null";
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return mixed
     */
    public function restrictExVatValue(CheckoutOrderBuilder $order, $errors)
    {
        $exVatValidator = new ExVatRestrictionValidator();
        $errors = $exVatValidator->validate($order, $errors);

        return $errors;
    }
}
