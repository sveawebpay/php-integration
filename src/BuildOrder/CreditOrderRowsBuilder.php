<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CreditOrderRowsBuilder is used to credit individual order rows in an order. 
 * 
 * For Invoice orders, the order row status of the order is updated
 * to reflect the new status of the order rows.
 * 
 * For Card and Direct bank orders, individual order rows will still reflect the 
 * status they got in order creation, even if orders have since been credited.
 *  
 * Use setInvoiceId() or setOrderId() to specify the Svea invoice or order id 
 * for invoice and card/direct bank orders, respectively.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use setRowToCredit or setRowsToCredit() to specify the order row(s) to credit. The order numbers
 * should correspond to those returned by i.e. WebPayAdmin::queryOrder;
 * 
 * For card or direct bank orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass 
 * in order rows (from i.e. queryOrder) that will be matched with set rows to cancel.
 *  
 * Should you wish to add additional credit order rows not found in the original order, you may
 * add them using addCreditOrderRow() or addCreditOrderRows(). These will be added to the rows
 * specified using setRow(s)ToCredit.
 *
 * Then use either creditInvoiceOrderRows(), creditCardOrderRows() or creditDirectBankOrderRows(), 
 * which ever matches the payment method used in the original order request.
 * 
 * The final doRequest() will send the creditOrderRows request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
    
    /** @var OrderRows[] $creditOrderRows  any additional new order rows to credit */
    public $creditOrderRows;

    /** @var NumberedOrderRows[] $numberedOrderRows  numbered order rows passed in for hosted service orders */
    public $numberedOrderRows;

    public function __construct($config) {
        $this->conf = $config;
        $this->rowsToCredit = array();         
        $this->creditOrderRows = array();
        $this->numberedOrderRows = array();
    }        

    /**
     * Required. Use same countryCode as in createOrder request.
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /** @var string $countryCode */
    public $countryCode;

    /**
     * Required.
     * @param string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE
     * @return $this
     */
    public function setOrderType($orderTypeAsConst) {
        $this->orderType = $orderTypeAsConst;
        return $this;
    }
    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE */
    public $orderType;    

    /**
     * Required for CreditInvoiceOrder() -- use InvoiceId recieved with deliverOrder response.
     * @param numeric $invoiceIdAsString
     * @return $this
     */
    public function setInvoiceId($invoiceIdAsString) {
        $this->invoiceId = $invoiceIdAsString;
        return $this;
    }
    /** numeric $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $invoiceId;
   
    /**
     * Required for CreditCardOrder() -- use the order id (transaction id) received with the createOrder response.
     * @param numeric $invoiceIdAsString
     * @return $this
     */
    public function setOrderId($invoiceIdAsString) {
        $this->orderId = $invoiceIdAsString;
        return $this;
    }
    /** numeric $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;
   
    
    /**
     * Invoice payments only! Required.
     * @param string DistributionType $distributionTypeAsConst  i.e. DistributionType::POST|DistributionType::EMAIL
     * @return $this
     */
    public function setInvoiceDistributionType($distributionTypeAsConst) {
        if ($distributionTypeAsConst != \DistributionType::EMAIL || $distributionTypeAsConst != \DistributionType::POST) {
            $distributionTypeAsConst = trim($distributionTypeAsConst);
            if (preg_match("/post/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \DistributionType::POST;
            } elseif (preg_match("/mail/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \DistributionType::EMAIL;
            } else {
                $distributionTypeAsConst = \DistributionType::POST;
            }
        }
        $this->distributionType = $distributionTypeAsConst;
        return $this;
    }
    /**
     * @var string  "Post" or "Email"
     */
    public $distributionType;    
        
    /**
     * Required.
     * @param numeric $rowNumber
     * @return $this
     */
    public function setRowToCredit( $rowNumber ) {
        $this->rowsToCredit[] = $rowNumber;
        return $this;
    }    
    
    /**
     * Convenience method to provide several row numbers at once.
     * @param int[] $rowNumbers
     * @return $this
     */
    public function setRowsToCredit( $rowNumbers ) {       
        $this->rowsToCredit = array_merge( $this->rowsToCredit, $rowNumbers );     
        return $this;
    } 
       
    /**
     * Optional -- add an order row to credit that was not present in the original order.
     * 
     * These rows will be credited in addition to the rows specified using setRow(s)ToCredit
     * 
     * @param OrderRow $row
     * @return $this
     */
    public function addCreditOrderRow( $row ) {
        $this->creditOrderRows[] = $row;
        return $this;
    }    
    
    /**
     * Optional -- convenience method to add serveral new roes at once.
     *  
     * These rows will be credited in addition to the rows specified using setRow(s)ToCredit
     * 
     * @param OrderRow[] $rows
     * @return $this
     */
    public function addCreditOrderRows( $rows ) {
        $this->creditOrderRows = array_merge( $this->creditOrderRows, $rows );
        return $this;
    }    
   
    /**
     * CreditCardOrderRows, CreditDirectBankOrderRows: Required - add information on a single numbered order row
     * 
     * When crediting card or direct bank order rows, you must pass in information about the row
     * along with the request. The rows are then matched with the order rows specified
     * using setRow(s)ToCredit(). 
     * 
     * Note: the card or direct bank order does not update the state of any cancelled order rows, only
     * the total order amount to be charged.     
     * 
     * @param \Svea\NumberedOrderRow $numberedOrderRows instance of NumberedOrderRow
     * @return $this
     */
    public function addNumberedOrderRow( $numberedOrderRow ) {
        $this->numberedOrderRows[] = $numberedOrderRow;
        return $this;
    }       
    
    /**
     * CreditCardOrderRows, CreditDirectBankOrderRows: Optional - convenience method to provide several numbered order rows at once.
     * 
     * @param \Svea\NumberedOrderRow[] $numberedOrderRows array of NumberedOrderRow
     * @return $this
     */
    public function addNumberedOrderRows( $numberedOrderRows ) {
        $this->numberedOrderRows = array_merge( $this->numberedOrderRows, $numberedOrderRows );
        return $this;
    }
    
    /**
     * Use creditInvoiceOrderRows() to credit rows to an Invoice order using AdminServiceRequest CreditOrderRows request
     * @return CreditOrderRowsRequest 
     */
    public function creditInvoiceOrderRows() {
        $this->setOrderType(\ConfigurationProvider::INVOICE_TYPE );
        return new AdminService\CreditOrderRowsRequest($this);
    }
    
    /**
     * Use creditCardOrderRows() to credit a Card order by the specified order row amounts using HostedRequests CreditTransaction request
     * 
     * @return CreditTransaction
     * @throws ValidationException  if addNumberedOrderRows() has not been used.
     */
    public function creditCardOrderRows() {
        $this->setOrderType(\ConfigurationProvider::HOSTED_ADMIN_TYPE);
                
        $this->validateCreditCardOrderRows();
        $sumOfRowAmounts = $this->calculateSumOfRowAmounts( $this->rowsToCredit, $this->numberedOrderRows, $this->creditOrderRows );
        
        $creditTransaction = new HostedService\CreditTransaction($this->conf);
        $creditTransaction
            ->setTransactionId($this->orderId)
            ->setCountryCode($this->countryCode)
            ->setCreditAmount($sumOfRowAmounts*100) // *100, as setAmountToLower wants minor currency
        ;           
        return $creditTransaction;
    }
    
    /**
     * Use creditCardOrderRows() to credit a Direct Bank order by the specified order row amounts using HostedRequests CreditTransaction request
     * 
     * @return CreditTransaction
     * @throws ValidationException  if addNumberedOrderRows() has not been used.
     */
    public function creditDirectBankOrderRows() {        
        return $this->creditCardOrderRows();        
    }
    
    private function validateCreditCardOrderRows() {    
        if( !isset($this->orderId) ) {
            $exceptionString = "orderId is required for creditCardOrderRows(). Use method setOrderId().";
            throw new ValidationException($exceptionString);
        }
        
        if(count($this->numberedOrderRows) == 0) {
            $exceptionString = "numberedOrderRows is required for creditCardOrderRows(). Use method addNumberedOrderRows().";
            throw new ValidationException($exceptionString);
        }
        if(count($this->rowsToCredit) == 0) {
            $exceptionString = "rowsToCredit is required for creditCardOrderRows(). Use method setRowToCredit() or setRowsToCredit().";
            throw new ValidationException($exceptionString);
        }
    }

    private function calculateSumOfRowAmounts( $rowIndexes, $numberedRows, $creditOrderRows ) {        
        $sum = 0.0;
        $unique_indexes = array_unique( $rowIndexes );
        foreach( $numberedRows as $numberedRow) {            
            if( in_array($numberedRow->rowNumber,$unique_indexes) ) {
                $sum += ($numberedRow->quantity * ($numberedRow->amountExVat * (1 + ($numberedRow->vatPercent/100))));
            }
        }
        if( count($creditOrderRows) > 0 ) {
            foreach( $creditOrderRows as $creditOrderRow) {            
                $sum += ($creditOrderRow->quantity * ($creditOrderRow->amountExVat * (1 + ($creditOrderRow->vatPercent/100))));
            }            
        }
        return $sum;
    }
}
