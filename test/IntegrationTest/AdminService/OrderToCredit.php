<?php


namespace Svea\WebPay\Test\IntegrationTest\AdminService;


/** helper class, used to return information about an order */
class OrderToCredit {
    var $orderId;
    var $invoiceId;
    var $contractNumber;

    public function __construct( $orderId, $invoiceId = NULL, $contractNumber = NULL ) {
        $this->orderId = $orderId;
        $this->invoiceId = $invoiceId;
        $this->contractNumber = $contractNumber;
    }
}
