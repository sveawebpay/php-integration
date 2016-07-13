<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service CancelOrder request response.
 *
 * @author Kristian Grossman-Madsen
 */
class CancelOrderResponse extends AdminServiceResponse
{
    /**
     * CancelOrderResponse constructor.
     * @param $message
     */
    function __construct($message)
    {
        $this->formatObject($message);
    }

    /**
     * Parses response and sets attributes.
     * @param $message
     */
    protected function formatObject($message)
    {
        parent::formatObject($message);

        if ($this->accepted == 1) {
            // nothing to do for cancelOrderRequest response
        }
    }
}
