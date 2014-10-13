<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CancelOrderRowsBuilder is used to cancel individual order rows in a unified manner.
 * 
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the new status of the order rows.
 * 
 * For Card orders, individual order rows will still reflect the status they got in 
 * order creation, even if orders have since been cancelled, and the order amount 
 * to be charged is simply lowered by sum of the order rows' amount.
 * 
 * Use setOrderId() to specify the Svea order id, this is the order id returned 
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use setRowToCancel or setRowsToCancel() to specify the order row(s) to cancel. The 
 * order numbers should correspond to those returned by i.e. WebPayAdmin::queryOrder;
 * 
 * For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in 
 * numbered order rows (from i.e. queryOrder) that will be matched with set rows to cancel.
 *  
 * (You can use the WebPayAdmin::queryOrder() entrypoint to get information about the order, the 
 * queryOrder response attribute numberedOrderRows contains the serverside order rows w/numbers.
 * Note: if card order rows has been changed (i.e. credited, cancelled) after initial creation, 
 * the returned rows may not be accurate.)
 *  
 * Then use either cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows or cancelCardOrderRows,
 * which ever matches the payment method used in the original order request.
 *  
 * The final doRequest() will send the queryOrder request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CancelOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
    
    /** @var int[] $rowsToCancel */
    public $rowsToCancel;

    /** @var NumberedOrderRows[] $numberedOrderRows */
    public $numberedOrderRows;

    /** @var string $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;    
    
    public function __construct($config) {
         $this->conf = $config;
         $this->rowsToCancel = array();
         $this->numberedOrderRows = array();
    }

    /**
     * Required for invoice or part payment orders -- use the order id (transaction id) recieved with the createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * Optional for card orders -- use the order id (transaction id) received with the createOrder response.
     * 
     * This is an alias for setOrderId().
     * 
     * @param string $orderIdAsString  
     * @return $this
     */
    public function setTransactionId($orderIdAsString) {
        return $this->setOrderId($orderIdAsString);
    }       
    
    /**
     * Required. Use same countryCode as in createOrder request.
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /** @var string $countryCode */
    public $countryCode;

    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_TYPE */
    public $orderType;    

    /**
     * Required - add a row number to cancel
     * @param int $rowNumber
     * @return $this
     */
    public function setRowToCancel( $rowNumber ) {
        $this->rowsToCancel[] = $rowNumber;
        return $this;
    }    
    
    /**
     * Optional - convenience method to provide several row numbers at once.
     * @param int[] $rowNumbers
     * @return $this
     */
    public function setRowsToCancel( $rowNumbers ) {
        $this->rowsToCancel = array_merge( $this->rowsToCancel, $rowNumbers );
        return $this;
    }    
    
    /**
     * Required - add information on a single numbered order row
     * 
     * When cancelling card order rows, you must pass in information about the row
     * along with the request. The rows are then matched with the order rows specified
     * using setRow(s)ToCredit(). 
     * 
     * Note: the card order does not update the state of any cancelled order rows, only
     * the total order amount to be charged.     
     * 
     * @param \Svea\NumberedOrderRow $numberedOrderRows instance of NumberedOrderRow
     * @return $this
     */
    public function addNumberedOrderRow( $rowNumber ) {
        $this->numberedOrderRows[] = $rowNumber;
        return $this;
    }       
    
    /**
     * Optional - Convenience method to provide several numbered order rows at once.
     * 
     * @param \Svea\NumberedOrderRow[] $numberedOrderRows array of NumberedOrderRow
     * @return $this
     */
    public function addNumberedOrderRows( $numberedOrderRows ) {
        $this->numberedOrderRows = array_merge( $this->numberedOrderRows, $numberedOrderRows );
        return $this;
    }

    /**
     * Use cancelInvoiceOrderRows() to cancel an Invoice order using AdminServiceRequest CancelOrderRows request
     * @return CancelOrderRowsRequest 
     */
    public function cancelInvoiceOrderRows() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new AdminService\CancelOrderRowsRequest($this);
    }
    
    /**
     * Use cancelPaymentPlanOrderRows() to cancel a PaymentPlan order using AdminServiceRequest CancelOrderRows request
     * @return CancelOrderRowsRequest 
     */
    public function cancelPaymentPlanOrderRows() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new AdminService\CancelOrderRowsRequest($this);    
    }

    /**
     * Use cancelCardOrderRows() to lower the amount of a Card order by the specified order row amounts using HostedRequests LowerTransaction request
     * 
     * @return LowerTransaction
     * @throws ValidationException  if addNumberedOrderRows() has not been used.
     */
    public function cancelCardOrderRows() {
        $this->orderType = \ConfigurationProvider::HOSTED_ADMIN_TYPE;
                
        $this->validateCancelCardOrderRows();
        $sumOfRowAmounts = $this->calculateSumOfRowAmounts( $this->rowsToCancel, $this->numberedOrderRows );
        
        $lowerTransaction = new HostedService\LowerTransaction($this->conf);
        $lowerTransaction->countryCode = $this->countryCode;
        $lowerTransaction->transactionId = $this->orderId;
        $lowerTransaction->amountToLower = $sumOfRowAmounts*100; // *100, as setAmountToLower wants minor currency
        return $lowerTransaction;
    }
    
    private function validateCancelCardOrderRows() {     
        if(count($this->numberedOrderRows) == 0) {
            $exceptionString = "numberedOrderRows is required for cancelCardOrderRows(). Use method addNumberedOrderRows().";
            throw new ValidationException($exceptionString);
        }
        if(count($this->rowsToCancel) == 0) {
            $exceptionString = "rowsToCancel is required for cancelCardOrderRows(). Use method setRowToCancel() or setRowsToCancel.";
            throw new ValidationException($exceptionString);
        }
    } 

    private function calculateSumOfRowAmounts( $rowIndexes, $numberedRows ) {
        $sum = 0.0;
        $unique_indexes = array_unique( $rowIndexes );
        foreach( $numberedRows as $numberedRow) { 
            if( in_array($numberedRow->rowNumber,$unique_indexes) ) {
                $sum += ($numberedRow->quantity * ($numberedRow->amountExVat * (1 + ($numberedRow->vatPercent/100))));
            }
        }
        return $sum;
    }
}
