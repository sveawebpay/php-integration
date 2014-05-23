<?php
namespace Svea;

require_once 'OrderRow.php'; // fix for class loader sequencing problem


/**
 * NumberedOrderRow is returned with the WebPayAdmin::QueryOrder() response, or 
 * directly via the service requests GetOrders, or QueryTransaction, responses.
 */
class NumberedOrderRow extends OrderRow {

    /** @var numeric $creditInvoiceId  reference to invoice to credit */
    public $creditInvoiceId;
    
    /** @var numeric $invoiceId  if order has been delivered, reference to resulting invoice */
    public $invoiceId;
    
    /** @var integer $rowNumber  the order row number, starting with 1 for the first order row */
    public $rowNumber;
    
    /** @var string $status  one of: NotDelivered | Delivered | Cancelled */
    public $status;
}
