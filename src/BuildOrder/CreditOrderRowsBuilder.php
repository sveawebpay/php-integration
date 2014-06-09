<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';


/**
 * CreditOrderRowsBuilder is used to credit individual order rows in an order. 
 * (I.e. partially credit an order).
 * 
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the credited order rows.
 * 
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use creditOrderRow() or creditOrderRows() to specify the credit order row(s).
 * 
 * Then use either creditInvoiceOrderRows() or creditPaymentPlanOrderRows(), or 
 * creditCardOrderRows() or creditDirectBankOrderRows(), which ever matches
 * the payment method used in the original order request.
 * 
 * The final doRequest() will send the creditOrderRows request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
    
    /** @var OrderRows[] $orderRows */
    public $orderRows;

    /** @var NumberedOrderRows[] $numberedOrderRows */
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
     * CreditInvoiceOrder(). Required. Use InvoiceId recieved with deliverOrder response.
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
     * CreditCardOrder(). Required. Use the order id (transaction id) received with the createOrder response.
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
     * Required.
     * @param OrderRow $row
     * @return $this
     */
    public function addCreditOrderRow( $row ) {
        $this->creditOrderRows[] = $row;
        return $this;
    }    
    
    /**
     * Convenience method to credit several rows at once.
     * @param OrderRow[] $rows
     * @return $this
     */
    public function addCreditOrderRows( $rows ) {
        $this->creditOrderRows = array_merge( $this->creditOrderRows, $rows );
        return $this;
    }    

    /**
     * CreditCardOrderRows: Required
     * When crediting card order rows, you must pass in an array of NumberedOrderRows
     * along with the request. This array is then matched with the order rows specified
     * with setRow(s)ToCredit.
     * 
     * Note: the card order does not update the state of any cancelled order rows, only
     * the total order amount to be charged.     
     */
    public function setNumberedOrderRows( $numberedOrderRows ) {
        $this->numberedOrderRows = $numberedOrderRows;
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
        $sumOfRowAmounts = $this->calculateSumOfRowAmounts( $this->rowsToCredit, $this->numberedOrderRows );
        
        $creditTransaction = new CreditTransaction($this->conf);
        $creditTransaction
            ->setTransactionId($this->orderId)
            ->setCountryCode($this->countryCode)
            ->setCreditAmount($sumOfRowAmounts*100) // *100, as setAmountToLower wants minor currency
        ;           
        return $creditTransaction;
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
