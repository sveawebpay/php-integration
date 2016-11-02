<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service CancelPaymentPlanRows request response.
 *
 * @author ann-hal for Svea Ekonomi Ab | Svea\WebPay\WebPay
 */
class CreditPaymentPlanResponse extends AdminServiceResponse
{
    /**
     * CreditPaymentPlanResponse constructor.
     * @param $message
     */
    function __construct($message)
    {
        $this->formatObject($message);
    }
}

// raw response message example:
//
//stdClass Object
//(
//    [ErrorMessage] =>
//    [ResultCode] => 0
//)