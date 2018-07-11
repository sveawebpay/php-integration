<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service AddOrderRows request response.
 *
 * @author Kristian Grossman-Madsen, Fredrik Sundell
 */
class AddOrderRowsResponse extends AdminServiceResponse
{
    /**
     * AddOrderRowsResponse constructor.
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
            // nothing to do for cancelOrderRequest response
        }
    }
}
