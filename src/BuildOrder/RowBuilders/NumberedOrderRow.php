<?php
namespace Svea;

require_once 'OrderRow.php'; // fix for class loader sequencing problem


/**
 * NumberedOrderRow is returned with the WebPayAdmin::QueryOrder() response, or 
 * directly via the service requests GetOrders, or QueryTransaction, responses.
 */
class NumberedOrderRow extends OrderRow {
    const ORDERROWSTATUS_NOTDELIVERED = 'NotDelivered';
    const ORDERROWSTATUS_DELIVERED = 'Delivered';
    const ORDERROWSTATUS_CANCELLED = 'Cancelled';
    
    /** @var numeric $creditInvoiceId  reference to invoice to credit */
    public $creditInvoiceId;
    
    /** @var numeric $invoiceId  if order has been delivered, reference to resulting invoice */
    public $invoiceId;
    
    /** @var integer $rowNumber  the order row number, starting with 1 for the first order row */
    public $rowNumber;
    
    /** @var string $status  one of: NotDelivered | Delivered | Cancelled */
    public $status;

    /**
     * Optional.
     * @param numeric $creditInvoiceIdAsNumeric
     * @return $this
     */
    public function setCreditInvoiceId($creditInvoiceIdAsNumeric) {
        $this->creditInvoiceId = $creditInvoiceIdAsNumeric;
        return $this;
    }    
    
    /**
     * Optional.
     * @param numeric $invoiceIdAsNumeric
     * @return $this
     */
    public function setinvoiceId($invoiceIdAsNumeric) {
        $this->invoiceId = $invoiceIdAsNumeric;
        return $this;
    }   
    
    /**
     * Optional.
     * @param numeric $rowNumberAsNumeric
     * @return $this
     */
    public function setRowNumber($rowNumberAsNumeric) {
        $this->rowNumber = $rowNumberAsNumeric;
        return $this;
    }   
    
    /**
     * Optional.
     * @param string $status
     * @return $this
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }   
    
}
