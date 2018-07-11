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
     * @param $logs
     */
    function __construct($message, $logs)
    {
        $this->formatObject($message, $logs);
    }
}

// raw response message example:
//
//stdClass Object
//(
//    [ErrorMessage] =>
//    [ResultCode] => 0
//)