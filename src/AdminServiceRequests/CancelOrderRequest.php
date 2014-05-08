<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service CancelOrderRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRequest extends AdminServiceRequest {
    
    // TODO mocked for now with a StdClass object containing attributes, will operate like other orderBuilder objects
    public $orderBuilder;

    /**
     * @param type $orderBuilder
     */
    public function __construct($cancelOrderBuilder) {
        $this->orderBuilder = $cancelOrderBuilder;
    }
    
    public function doRequest() {
        
    }
}