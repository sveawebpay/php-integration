<?php
namespace Svea;

require_once 'HandleOrder.php';

/**
 * Update Created Invoiceorder with additional information and prepare it for delivery.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/HandleOrder
 */
class DeliverInvoice extends HandleOrder {

    /**
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }
}
