<?php
namespace Svea\AdminService;

require_once 'AdminServiceResponse.php';

/**
 * Handles the Svea Admin Web Service CancelOrderRowsResponse request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRowsResponse extends AdminServiceResponse {
  
    function __construct($message) {
        $this->formatObject($message);  
    }

    /**
     * Parses response and sets attributes.
     */    
    protected function formatObject($message) {
        parent::formatObject($message);
        
        if ($this->accepted == 1) {
            // nothing to do for cancelOrderRequest response
        }
    }
}
