<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service CancelAccountCreditAmount request response.
 *
 * @author Svea Ekonomi Ab | Svea\WebPay\WebPay
 */
class CancelAccountCreditRows extends AdminServiceResponse
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