<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * The WebPayAdmin::deliverOrderRows entrypoint method is used to partially deliver an 
 * order. Supports Invoice orders. (To partially deliver a Payment Plan order, contact 
 * Svea customer service. Card or Direct Bank orders are not supported.)
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class DeliverOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;

    /** @var numeric $orderId  card/direct bank order transaction id as returned in the createOrder request response,  */
    public $orderId;   
    
    /** @var string $countryCode */
    public $countryCode;
        
//    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::HOSTED_ADMIN_TYPE */
//    public $orderType;    
//    
//    /** @var OrderRows[] $deliverOrderRows  any additional new order rows to deliver */
//    public $deliverOrderRows;
//
//    /** @var int[] $rowsToDeliver  array of original order row indexes to deliver
//    public $rowsToDeliver;
//    
//    /** @var NumberedOrderRows[] $numberedOrderRows  numbered order rows passed in for hosted service orders */
//    public $numberedOrderRows;
//    
//    /** @var numeric @invoiceId  invoice id as returned in the deliverOrder request response */
//    public $invoiceId;
//    
//
//    /**@var string  "Post" or "Email" */
//    public $distributionType;    
    
    public function __construct($config) {
        $this->conf = $config;
        $this->deliverOrderRows = array();
        $this->rowsToDeliver = array();         
//        $this->numberedOrderRows = array();
    }        

    /**
     * Required -- use same countryCode as in createOrder request
     * 
     * Use setCountryCode() to specify the country code matching the original 
     * createOrder request.
     * 
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

//    /**
//     * Required for DeliverInvoiceOrder() -- use invoice id recieved with deliverOrder response.
//     * 
//     * Use setInvoiceId() to set the invoice to deliver. Use setOrderId() to set the 
//     * card or direct bank transaction to deliver.
//     * 
//     * @param numeric $invoiceIdAsString
//     * @return $this
//     */
//    public function setInvoiceId($invoiceIdAsString) {
//        $this->invoiceId = $invoiceIdAsString;
//        return $this;
//    }
//   
    /**
     * Required for DeliverCardOrder() -- use the order id (transaction id) received with the createOrder response.
     * 
     * @param numeric $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    
//    /**
//     * Optional for DeliverCardOrder() -- use the order id (transaction id) received with the createOrder response.
//     * 
//     * This is an alias for setOrderId().
//     * 
//     * @param numeric $orderIdAsString
//     * @return $this
//     */
//    public function setTransactionId($orderIdAsString) {
//        return $this->setOrderId($orderIdAsString);
//    }    
//    
//    /**
//     * Required for DeliverInvoiceOrder() -- must match the invoice distribution type for the order
//     * 
//     * @param string DistributionType $distributionTypeAsConst  i.e. DistributionType::POST|DistributionType::EMAIL
//     * @return $this
//     */
//    public function setInvoiceDistributionType($distributionTypeAsConst) {
//        if ($distributionTypeAsConst != \DistributionType::EMAIL || $distributionTypeAsConst != \DistributionType::POST) {
//            $distributionTypeAsConst = trim($distributionTypeAsConst);
//            if (preg_match("/post/i", $distributionTypeAsConst)) {
//                $distributionTypeAsConst = \DistributionType::POST;
//            } elseif (preg_match("/mail/i", $distributionTypeAsConst)) {
//                $distributionTypeAsConst = \DistributionType::EMAIL;
//            } else {
//                $distributionTypeAsConst = \DistributionType::POST;
//            }
//        }
//        $this->distributionType = $distributionTypeAsConst;
//        return $this;
//    }
//        
//    /**
//     * Required -- a row number to deliver
//     * 
//     * Use setRowToDeliver() or setrowsToDeliver() to specify order rows to deliver. 
//     * The given row numbers must correspond with the serverside row numbers. 
//     * For card or direct bank orders, must match an order row specified using
//     * addNumberedOrderRow() or addNumberedOrderRows().
//     * 
//     * @param numeric $rowNumber
//     * @return $this
//     */
//    public function setRowToDeliver( $rowNumber ) {
//        $this->rowsToDeliver[] = $rowNumber;
//        return $this;
//    }    
//    
//    /**
//     * Optional -- convenience method to provide several row numbers at once.
//     * 
//     * Use setRowToDeliver() or setrowsToDeliver() to specify order rows to deliver. 
//     * The given row numbers must correspond with the serverside row numbers. 
//     * For card or direct bank orders, must match an order row specified using
//     * addNumberedOrderRow() or addNumberedOrderRows().
//     * 
//     * @param int[] $rowNumbers
//     * @return $this
//     */
//    public function setrowsToDeliver( $rowNumbers ) {       
//        $this->rowsToDeliver = array_merge( $this->rowsToDeliver, $rowNumbers );     
//        return $this;
//    } 
//       
//    /**
//     * Optional -- add an order row to deliver that was not present in the original order.
//     * 
//     * These rows will be delivered in addition to the rows specified using setRow(s)ToDeliver
//     * 
//     * @param OrderRow $row
//     * @return $this
//     */
//    public function addDeliverOrderRow( $row ) {
//        $this->deliverOrderRows[] = $row;
//        return $this;
//    }    
//    
//    /**
//     * Optional -- convenience method to add serveral new roes at once.
//     *  
//     * These rows will be delivered in addition to the rows specified using setRow(s)ToDeliver
//     * 
//     * @param OrderRow[] $rows
//     * @return $this
//     */
//    public function addDeliverOrderRows( $rows ) {
//        $this->deliverOrderRows = array_merge( $this->deliverOrderRows, $rows );
//        return $this;
//    }    
//   
//    /**
//     * DeliverCardOrderRows, DeliverDirectBankOrderRows: Required - add information on a single numbered order row
//     * 
//     * When delivering card or direct bank order rows, you must pass in information about the row
//     * along with the request. The rows are then matched with the order rows specified
//     * using setRow(s)ToDeliver(). 
//     *   
//     * Use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
//     * the queryOrder response numberedOrderRows attribute contains the order rows and
//     * their numbers.
//     * 
//     * When used with card or direct bank orders the following limitations apply: 
//     * You need to supply the NumberedOrderRows on which to operate. These may be 
//     * fetched using the queryOrder method, but if the order has been edited after 
//     * creation they may not be accurate.
//     * 
//     * @param \Svea\NumberedOrderRow $numberedOrderRows instance of NumberedOrderRow
//     * @return $this
//     */
//    public function addNumberedOrderRow( $numberedOrderRow ) {
//        $this->numberedOrderRows[] = $numberedOrderRow;
//        return $this;
//    }       
//    
//    /**
//     * DeliverCardOrderRows, DeliverDirectBankOrderRows: Optional - convenience method to provide several numbered order rows at once.
//
//     * When delivering card or direct bank order rows, you must pass in information about the row
//     * along with the request. The rows are then matched with the order rows specified
//     * using setRow(s)ToDeliver(). 
//     *   
//     * Use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
//     * the queryOrder response numberedOrderRows attribute contains the order rows and
//     * their numbers.
//     * 
//     * When used with card or direct bank orders the following limitations apply: 
//     * You need to supply the NumberedOrderRows on which to operate. These may be 
//     * fetched using the queryOrder method, but if the order has been edited after 
//     * creation they may not be accurate.
//     * 
//     * @param \Svea\NumberedOrderRow[] $numberedOrderRows array of NumberedOrderRow
//     * @return $this
//     */
//    public function addNumberedOrderRows( $numberedOrderRows ) {
//        $this->numberedOrderRows = array_merge( $this->numberedOrderRows, $numberedOrderRows );
//        return $this;
//    }
//    
//    /**
//     * Use deliverInvoiceOrderRows() to deliver rows to an Invoice order using AdminServiceRequest DeliverOrderRows request
//     * @return DeliverOrderRowsRequest 
//     */
//    public function deliverInvoiceOrderRows() {
//        $this->orderType = \ConfigurationProvider::INVOICE_TYPE; 
//        
//        // validation is done in DeliverOrderRowsRequest
//      
//        return new AdminService\DeliverOrderRowsRequest($this);
//    }
//    
//    /**
//     * Use deliverCardOrderRows() to deliver a Card order by the specified order row amounts using HostedRequests DeliverTransaction request
//     * 
//     * @return DeliverTransaction
//     * @throws ValidationException  if addNumberedOrderRows() has not been used.
//     */
//    public function deliverCardOrderRows() {
//        $this->orderType = \ConfigurationProvider::HOSTED_ADMIN_TYPE;
//                
//        $this->validateDeliverCardOrderRows();
//        
//        $sumOfRowAmounts = $this->calculateSumOfRowAmounts( $this->rowsToDeliver, $this->numberedOrderRows, $this->deliverOrderRows );
//        
//        $deliverTransaction = new HostedService\DeliverTransaction($this->conf);
//        $deliverTransaction->transactionId = $this->orderId;
//        $deliverTransaction->deliverAmount = $sumOfRowAmounts*100; // *100, as setAmountToLower wants minor currency
//        $deliverTransaction->countryCode = $this->countryCode;
//        return $deliverTransaction;
//    }
//    
//    /**
//     * Use deliverDirectBankOrderRows() to deliver a Direct Bank order by the specified order row amounts using HostedRequests DeliverTransaction request
//     * 
//     * @return DeliverTransaction
//     * @throws ValidationException  if addNumberedOrderRows() has not been used.
//     */
//    public function deliverDirectBankOrderRows() {        
//        return $this->deliverCardOrderRows();        
//    }
//
//    /** 
//     * @internal 
//     */
//    private function validateDeliverCardOrderRows() {    
//        if( !isset($this->orderId) ) {
//            $exceptionString = "orderId is required for deliverCardOrderRows(). Use method setOrderId().";
//            throw new ValidationException($exceptionString);
//        }
//        
//        if( (count($this->rowsToDeliver) == 0) && (count($this->deliverOrderRows) == 0) ) {
//            $exceptionString = "at least one of rowsToDeliver or deliverOrderRows must be set. Use setRowToDeliver() or addDeliverOrderRow().";
//            throw new ValidationException($exceptionString);
//        }   
//        
//        if( (count($this->rowsToDeliver) > 0) && ( (count($this->rowsToDeliver) != count($this->numberedOrderRows)) ) ) {
//            $exceptionString = "every entry in rowsToDeliver must have a corresponding numberedOrderRows. Use setrowsToDeliver() and addNumberedOrderRow().";
//            throw new ValidationException($exceptionString);
//        }
//        
//        // validate that indexes matches entries
//        $numberedOrderRowNumbers = array_map( function($nrow){ return $nrow->rowNumber; }, $this->numberedOrderRows );
//               
//        foreach( $this->rowsToDeliver as $index ) {
//            if( !in_array($index, $numberedOrderRowNumbers) ) {
//                $exceptionString = "every entry in rowsToDeliver must match a numberedOrderRows. Use setrowsToDeliver() and addNumberedOrderRow().";
//            throw new ValidationException($exceptionString);
//            }
//        }
//    }
//
//    /** @internal */
//    private function calculateSumOfRowAmounts( $rowIndexes, $numberedRows, $deliverOrderRows ) {        
//        $sum = 0.0;
//        $unique_indexes = array_unique( $rowIndexes );
//        foreach( $numberedRows as $numberedRow) {            
//            if( in_array($numberedRow->rowNumber,$unique_indexes) ) {
//                $sum += ($numberedRow->quantity * ($numberedRow->amountExVat * (1 + ($numberedRow->vatPercent/100))));
//            }
//        }
//        if( count($deliverOrderRows) > 0 ) {
//            foreach( $deliverOrderRows as $deliverOrderRow) {            
//                $sum += ($deliverOrderRow->quantity * ($deliverOrderRow->amountExVat * (1 + ($deliverOrderRow->vatPercent/100))));
//            }            
//        }
//        return $sum;
//    }
}
