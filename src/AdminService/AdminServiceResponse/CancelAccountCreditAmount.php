<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service CancelAccountCreditAmount request response.
 *
 * @author Svea Ekonomi Ab | Svea\WebPay\WebPay
 */
class CancelAccountCreditAmount extends AdminServiceResponse
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