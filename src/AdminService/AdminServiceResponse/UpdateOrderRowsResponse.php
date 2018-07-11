<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service UpdateOrderRows request response.
 *
 * @author Kristian Grossman-Madsen, Fredrik
 */
class UpdateOrderRowsResponse extends AdminServiceResponse
{
    /**
     * UpdateOrderRowsResponse constructor.
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
