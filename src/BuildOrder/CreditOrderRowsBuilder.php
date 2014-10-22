<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * The WebPayAdmin::creditOrderRows entrypoint method is used to credit rows in an order after it has been delivered.
 * Supports Invoice, Card and Direct Bank orders. (To credit a Payment Plan order, contact Svea customer service.)
 * 
 * To credit an order row in full, you specify the index of the order row to 
 * credit (and for card orders, supply the numbered order row data itself).
 * 
 * If you wish to credit an amount not present in the original order, you need 
 * to supply new order row(s) for the credited amount using addCreditOrderRow() 
 * or addCreditOrderRows(). These rows will then be credited in addition to any 
 * rows specified using setRow(s)ToCredit below.
 *  
 * Use setInvoiceId() to specify the invoice (delivered order) to credit. 
 * 
 * Use setOrderId() to specify the card or direct bank transaction (delivered order) to credit.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use setRowToCredit() or setRowsToCredit() to specify order rows to credit. 
 * The given row numbers must correspond with the the serverside row number. 
 * 
 * For card or direct bank orders, it is required to use addNumberedOrderRow() 
 * or addNumberedOrderRows() to pass in a copy of the serverside order row data.
 *
 * You can use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
 * the queryOrder response numberedOrderRows attribute contains the order rows with numbers.
 * For invoice orders, the serverside order rows is updated after a creditOrderRows request. 
 * Note that for Card and Direct bank orders the serverside order rows will not be updated.
 *  
 * Then use either creditInvoiceOrderRows(), creditCardOrderRows() or 
 * creditDirectBankOrderRows() to get a request object, which ever matches the 
 * payment method used in the original order.
 * 
 * Calling doRequest() on the request object will send the request to Svea and 
 * return either a CreditOrderRowsResponse or a CreditTransactionResponse.
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;

    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::HOSTED_ADMIN_TYPE */
    public $orderType;    
    
    /** @var OrderRows[] $creditOrderRows  any additional new order rows to credit */
    public $creditOrderRows;

    /** @var int[] $rowsToCredit  array of original order row indexes to credit */
    public $rowsToCredit;
    
    /** @var NumberedOrderRows[] $numberedOrderRows  numbered order rows passed in for hosted service orders */
    public $numberedOrderRows;
    
    /** @var string @invoiceId  invoice id as returned in the deliverOrder request response */
    public $invoiceId;
    
    /** @var string $orderId  card/direct bank order transaction id as returned in the createOrder request response,  */
    public $orderId;   
    
    /** @var string $countryCode */
    public $countryCode;

    /**@var string  "Post" or "Email" */
    public $distributionType;    
    
    public function __construct($config) {
        $this->conf = $config;
        $this->creditOrderRows = array();
        $this->rowsToCredit = array();         
        $this->numberedOrderRows = array();
    }

    /**
     * Required -- use same countryCode as in createOrder request
     * 
     * Use setCountryCode() to specify the country code matching the original 
     * createOrder request.
     * 
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required for creditInvoiceOrder() -- use invoice id recieved with deliverOrder response.
     * 
     * Use setInvoiceId() to set the invoice to credit. Use setOrderId() to set the 
     * card or direct bank transaction to credit.
     * 
     * @param string $invoiceIdAsString
     * @return $this
     */
    public function setInvoiceId($invoiceIdAsString) {
        $this->invoiceId = $invoiceIdAsString;
        return $this;
    }
   
    /**
     * Required for creditCardOrder() -- use the order id (transaction id) received with the createOrder response.
     * 
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    
    /**
     * Optional for creditCardOrder() -- use the order id (transaction id) received with the createOrder response.
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
     * Required for creditInvoiceOrder() -- must match the invoice distribution type for the order
     * 
     * @param string DistributionType $distributionTypeAsConst  i.e. DistributionType::POST|DistributionType::EMAIL
     * @return $this
     */
    public function setInvoiceDistributionType($distributionTypeAsConst) {
        $this->distributionType = $distributionTypeAsConst;
        return $this;
    }
    
    /**
     * Required -- a row number to credit
     * 
     * Use setRowToCredit() or setRowsToCredit() to specify order rows to credit. 
     * The given row numbers must correspond with the the serverside row number. 
     * 
     * @param string $rowNumber
     * @return $this
     */
    public function setRowToCredit( $rowNumber ) {
        $this->rowsToCredit[] = $rowNumber;
        return $this;
    }    
    
    /**
     * Optional -- convenience method to provide several row numbers at once.
     * 
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
     * Optional -- convenience method to add serveral new rows at once.
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
     * Use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
     * the queryOrder response numberedOrderRows attribute contains the order rows and
     * their numbers.
     * 
     * When used with card or direct bank orders the following limitations apply: 
     * You need to supply the NumberedOrderRows on which to operate. These may be 
     * fetched using the queryOrder method, but if the order has been edited after 
     * creation they may not be accurate.
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
     * @return \Svea\AdminService\CreditOrderRowsRequest
     */
    public function creditInvoiceOrderRows() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE; 
        
        // validation is done in CreditOrderRowsRequest
      
        return new AdminService\CreditOrderRowsRequest($this);
    }
    
    /**
     * Use creditCardOrderRows() to credit a Card order by the specified order row amounts using HostedRequests CreditTransaction request
     * 
     * @return \Svea\HostedService\CreditTransaction
     * @throws ValidationException  if addNumberedOrderRows() has not been used.
     */
    public function creditCardOrderRows() {
        $this->orderType = \ConfigurationProvider::HOSTED_ADMIN_TYPE;
                
        // we need to validate card on this level before translating attributes to those relevant to hosted admin functions
        $this->validateCreditCardOrderRows();
        
        $sumOfRowAmounts = $this->calculateSumOfRowAmounts( $this->rowsToCredit, $this->numberedOrderRows, $this->creditOrderRows );
        
        $creditTransaction = new HostedService\CreditTransaction($this->conf);
        $creditTransaction->transactionId = $this->orderId;
        $creditTransaction->creditAmount = $sumOfRowAmounts*100; // *100, as setAmountToLower wants minor currency
        $creditTransaction->countryCode = $this->countryCode;
        return $creditTransaction;
    }
    
    /**
     * Use creditDirectBankOrderRows() to credit a Direct Bank order by the specified order row amounts using HostedRequests CreditTransaction request
     * 
     * @return \Svea\HostedService\CreditTransaction
     * @throws ValidationException  if addNumberedOrderRows() has not been used.
     */
    public function creditDirectBankOrderRows() {        
        return $this->creditCardOrderRows();        
    }

    /** 
     * @internal 
     */
    private function validateCreditCardOrderRows() {    
        if( !isset($this->orderId) ) {
            $exceptionString = "orderId is required for creditCardOrderRows(). Use method setOrderId().";
            throw new ValidationException($exceptionString);
        }
        
        if( !isset($this->countryCode) ) {
            $exceptionString = "countryCode is required for creditCardOrderRows(). Use method setCountryCode().";
            throw new ValidationException($exceptionString);
        }
        
        if( (count($this->rowsToCredit) == 0) && (count($this->creditOrderRows) == 0) ) {
            $exceptionString = "at least one of rowsToCredit or creditOrderRows must be set. Use setRowToCredit() or addCreditOrderRow().";
            throw new ValidationException($exceptionString);
        }   
        
        if( (count($this->rowsToCredit) > 0) && ( (count($this->rowsToCredit) != count($this->numberedOrderRows)) ) ) {
            $exceptionString = "every entry in rowsToCredit must have a corresponding numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().";
            throw new ValidationException($exceptionString);
        }
        
        // validate that indexes matches entries
        $numberedOrderRowNumbers = array_map( function($nrow){ return $nrow->rowNumber; }, $this->numberedOrderRows );
               
        foreach( $this->rowsToCredit as $index ) {
            if( !in_array($index, $numberedOrderRowNumbers) ) {
                $exceptionString = "every entry in rowsToCredit must match a numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().";
            throw new ValidationException($exceptionString);
            }
        }
    }

    /** @internal */
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
