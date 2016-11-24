<?php

namespace Svea\WebPay\Checkout\Response;

use Svea\WebPay\Checkout\Response\Model\OrderRow;

class CheckoutResponseHelper
{
    public static function processData($data)
    {
        $response = $data;

        if (isset($response['Cart']['Items']) && is_array($response['Cart']['Items'])) {
            $newItems  = array();
            $cartItems = $response['Cart']['Items'];

            foreach ($cartItems as $item) {
                $orderRow = new OrderRow();
                $orderRow->map($item);
                $newItems[] = $orderRow->getRefactoredData();
            }

            $response['Cart']['Items'] = $newItems;
        }

        return $response;
    }
}
