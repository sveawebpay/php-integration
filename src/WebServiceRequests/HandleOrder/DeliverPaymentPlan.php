<?php
namespace Svea;

/**
 * Update Created PaymentPlanorder with additional information and prepare it for delivery.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/HandleOrder
 */
class DeliverPaymentPlan extends HandleOrder {

    /**
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }
}
