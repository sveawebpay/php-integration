<?php

namespace Svea\WebPay\Checkout\Validation;

class ExVatRestrictionValidator
{
    public function validate($order, $errors)
    {
        foreach ($order->rows as $orderItem)
        {
            if($orderItem->amountExVat != null)
            {
                $errors['exVatAmountRestriction'] = 'AmountExVat is not allowed for checkout functions, please use only AmountIncVat';
            }
        }

        return $errors;
    }
}
