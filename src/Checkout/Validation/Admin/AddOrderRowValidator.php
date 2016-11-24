<?php

namespace Svea\WebPay\Checkout\Validation\Admin;

use Svea\WebPay\BuildOrder\AddOrderRowsBuilder;
use Svea\WebPay\BuildOrder\Validator\OrderValidator;

class AddOrderRowValidator extends OrderValidator
{
    public $errors = array();

    /**
     * @param AddOrderRowsBuilder $adminBuilder
     * @return array
     */
    public function validate($adminBuilder)
    {
        $errors = $this->errors;

        $orderId = $adminBuilder->orderId;
        $orderRow = $adminBuilder->orderRows;

        if (empty($orderId) || !is_int($orderId)) {
            $errors['incorrect Order Id'] = "Order Id can't be empty and must be Integer";
        }

        if (empty($orderRow)) {
            $errors['incorrect Order Row data'] = "Order Row data can't be empty and must be Array";
        }

        if (count($orderRow) > 1) {
            $errors['incorrect Order Row data'] = "You can Add just one Order Row";
        }

        $errors = $this->validateOrderRows($adminBuilder, $errors);

        return $errors;
    }

    /**
     * @param object $order
     * @param array  $errors
     * @return array
     */
    protected function validateOrderRows($order, $errors)
    {
        $errors = parent::validateOrderRows($order, $errors);
        $errors = $this->validateCheckoutOrderRows($order, $errors);

        if (empty($order->orderRows[0]->amountIncVat)) {
            $errors['incorrect Order Row data'] = "This function support only amountIncVat you need to use ->setAmountIncVat()";
        }

        foreach ($order->orderRows as $row) {
            if (isset($row->discountPercent)) {
                if (!is_int($row->discountPercent) || ($row->discountPercent < 0 || $row->discountPercent > 99)) {
                    $errors['bad discount percent'] = "Discount percent must be integer in value range of 0-99";
                }
            }
        }

        return $errors;
    }
}
