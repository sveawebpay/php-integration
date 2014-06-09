<?php
namespace Svea\WebService;

require_once 'WebServicePayment.php';

/**
 * Creates Payment Plan Order. Extends WebServicePayment
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanPayment extends WebServicePayment {

    public $orderType = 'PaymentPlan';

    public function __construct($order) {
        parent::__construct($order);
    }

    protected function setOrderType($orderInformation) {
        $orderInformation->AddressSelector = "";
        $orderInformation->OrderType = $this->orderType;
        return $orderInformation;
    }
}
