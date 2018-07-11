<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service UpdateOrder request response.
 */
class UpdateOrderResponse extends AdminServiceResponse
{
    /**
     * UpdateOrderResponse constructor.
     * @param $message
     * @param $logs
     */
    function __construct($message, $logs)
    {
        $this->formatObject($message, $logs);
    }

    /**
     * Parses response and sets attributes.
     * @param $message
     * @param $logs
     */
    protected function formatObject($message, $logs)
    {
        parent::formatObject($message, $logs);

        if ($this->accepted == 1) {
            // nothing to do for updateOrderRequest response
        }
    }
}
