<?php

namespace Svea\WebPay\Checkout\Validation\Admin;

use Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder;
use Svea\WebPay\BuildOrder\Validator\OrderValidator;

class UpdateOrderRowValidator extends OrderValidator
{
    public $errors = array();

    /**
     * @param UpdateOrderRowsBuilder $adminBuilder
     * @return array
     */
    public function validate($adminBuilder)
    {
        $errors = $this->errors;

        $orderId = $adminBuilder->orderId;
        $orderRow = $adminBuilder->numberedOrderRows;

        if (empty($orderId) || !is_int($orderId)) {
            $errors['incorrect Order Id'] = "Order Id can't be empty and must be Integer";
        }

        if (count($orderRow) == 0) {
            $errors['incorrect Order Row data'] = "Order Row data can't be empty and must be Array";

            return $errors;
        }

        if (count($orderRow) > 1) {
            $errors['incorrect Order Row data'] = "You can Update just one Order Row";

            return $errors;
        }

        $errors = $this->validateOrderRows($adminBuilder, $errors);

        return $errors;
    }

    /**
     * @param object $order
     * @param array $errors
     * @return array
     */
    protected function validateOrderRows($order, $errors)
    {
        $errors = parent::validateOrderRows($order, $errors);
        $errors = $this->validateCheckoutOrderRows($order, $errors);

        foreach ($order->numberedOrderRows as $row) {
            if (isset($row->discountPercent)) {
                if (!is_int($row->discountPercent) || ($row->discountPercent < 0 || $row->discountPercent > 99)) {
                    $errors['bad discount percent'] = "Discount percent must be integer in value range of 0-99";
                }
            }
            if (isset($row->amountIncVat)) {
                if (!is_float($row->amountIncVat) && $row->amountIncVat !== 0) {
                    $errors['missing values'] = "amountIncVat is not of type float.";
                }
            }
        }

        return $errors;
    }
}
