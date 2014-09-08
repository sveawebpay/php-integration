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
        
        $this->validateDeliverInvoiceOrderRows();
        
        return new AdminService\DeliverOrderRowsRequest($this);
    }

    /** 
     * @internal 
     */
    private function validateDeliverInvoiceOrderRows() {    
        if( !isset($this->orderId) ) {
            $exceptionString = "orderId is required for deliverInvoiceOrderRows(). Use method setOrderId().";
            throw new ValidationException($exceptionString);
        }

        if( !isset($this->countryCode) ) {
            $exceptionString = "countryCode is required for deliverInvoiceOrderRows(). Use method setCountryCode().";
            throw new ValidationException($exceptionString);
        }        
        
        if( !isset($this->distributionType) ) {
            $exceptionString = "distributionType is required for deliverInvoiceOrderRows(). Use method setInvoiceDistributionType().";
            throw new ValidationException($exceptionString);
        } 
        
        if( (count($this->rowsToDeliver) == 0) ) {
            $exceptionString = "rowsToDeliver is required for deliverInvoiceOrderRows(). Use methods setRowToDeliver() or setRowsToDelvier().";
            throw new ValidationException($exceptionString);
        }           
    }
}
