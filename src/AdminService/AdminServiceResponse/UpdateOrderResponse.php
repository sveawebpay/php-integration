<?php
namespace Svea\AdminService;

require_once 'AdminServiceResponse.php';

/**
 * Handles the Svea Admin Web Service UpdateOrder request response.
 */
class UpdateOrderResponse extends AdminServiceResponse {

    function __construct($message) {
        $this->formatObject($message);
    }

    /**
     * Parses response and sets attributes.
     */
    protected function formatObject($message) {
        parent::formatObject($message);

        if ($this->accepted == 1) {
            // nothing to do for updateOrderRequest response
        }
    }
}
