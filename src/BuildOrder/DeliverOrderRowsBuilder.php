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

    /** @var numeric $orderId  order id/transaction id as returned in the createOrder request response,  */
    public $orderId;   
    
    /** @var string $countryCode */
    public $countryCode;
        
    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::HOSTED_ADMIN_TYPE */
    public $orderType;    

    /** @var string $distributionType -- one of DistributionType::POST, ::EMAIL */
    public $distributionType;
           
    /** @var int[] $rowsToDeliver  array of original order row indexes to deliver */
    public $rowsToDeliver;   
    
    public function __construct($config) {
        $this->conf = $config;
        $this->deliverOrderRows = array();
        $this->rowsToDeliver = array();         
        $this->numberedOrderRows = array();
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
 
    /**
     * Required -- use the order id (transaction id) received with the createOrder response.
     * 
     * @param numeric $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    
    /**
     * Required -- must match the invoice distribution type for the order
     * 
     * @param string DistributionType $distributionTypeAsConst  i.e. DistributionType::POST|DistributionType::EMAIL
     * @return $this
     */
    public function setInvoiceDistributionType($distributionTypeAsConst) {
        $this->distributionType = $distributionTypeAsConst;
        return $this;
    }     
           
    /**
     * Required -- a row number to deliver
     * 
     * Use setRowToDeliver() or setRowsToDeliver() to specify order rows to deliver. 
     * The given row numbers must correspond with the serverside row numbers. 
     * For card or direct bank orders, must match an order row specified using
     * addNumberedOrderRow() or addNumberedOrderRows().
     * 
     * @param numeric $rowNumber
     * @return $this
     */
    public function setRowToDeliver( $rowNumber ) {
        $this->rowsToDeliver[] = $rowNumber;
        return $this;
    }    
    
    /**
     * Optional -- convenience method to provide several row numbers at once.
     * 
     * Use setRowToDeliver() or setRowsToDeliver() to specify order rows to deliver. 
     * The given row numbers must correspond with the serverside row numbers. 
     * For card or direct bank orders, must match an order row specified using
     * addNumberedOrderRow() or addNumberedOrderRows().
     * 
     * @param int[] $rowNumbers
     * @return $this
     */
    public function setRowsToDeliver( $rowNumbers ) {       
        $this->rowsToDeliver = array_merge( $this->rowsToDeliver, $rowNumbers );     
        return $this;
    } 

    /**
     * Use deliverInvoiceOrderRows() to deliver rows to an Invoice order using AdminServiceRequest DeliverOrderRows request
     * @return DeliverOrderRowsRequest 
     */
    public function deliverInvoiceOrderRows() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE; 
        
        // validation is done in DeliverOrderRowsRequest
      
        return new AdminService\DeliverOrderRowsRequest($this);
    }

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
//            $exceptionString = "every entry in rowsToDeliver must have a corresponding numberedOrderRows. Use setRowsToDeliver() and addNumberedOrderRow().";
//            throw new ValidationException($exceptionString);
//        }
//        
//        // validate that indexes matches entries
//        $numberedOrderRowNumbers = array_map( function($nrow){ return $nrow->rowNumber; }, $this->numberedOrderRows );
//               
//        foreach( $this->rowsToDeliver as $index ) {
//            if( !in_array($index, $numberedOrderRowNumbers) ) {
//                $exceptionString = "every entry in rowsToDeliver must match a numberedOrderRows. Use setRowsToDeliver() and addNumberedOrderRow().";
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
