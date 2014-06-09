<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * OrderBuilder collects and prepares order data to be sent to Svea. It is used
 * by createOrderBuilder and DeliverOrderBuilder.
 * 
 * Set all required order attributes in CreateOrderBuilder instance by using the 
 * instance setAttribute() methods. Instance methods can be chained together, as 
 * they return the instance itself in a fluent fashion.
 * 
 * Finish setting order attributes by chosing a payment method using one of the
 * usePaymentMethod() methods below. You can then go on specifying any payment 
 * method specific settings, see methods provided by the returned payment class.
 * 
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderBuilder {
    /** @var boolean  true indicates test mode, false indicates production mode */
    public $testmode = false;
    
    /**  
     * @param ConfigurationProvider $config 
     */
    public function __construct($config) {
        $this->conf = $config;
    }
    /** @var type */
    public $conf;

    /**
     * @param mixed $itemCustomerObject  accepts IndividualIdentity or CompanyIdentity object
     * @return $this
     */
     public function addCustomerDetails($itemCustomerObject) {
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }
    /** @var IndividualCustomer|CompanyCustomer */
    public $customerIdentity;
    
    /**
     * @param OrderRow $orderRow
     * @return $this
     */
    public function addOrderRow($itemOrderRowObject) {
        if (is_array($itemOrderRowObject)) {
            foreach ($itemOrderRowObject as $row) {
                array_push($this->orderRows, $row);
            }
        } else {
             array_push($this->orderRows, $itemOrderRowObject);
        }
       return $this;
    }
    /** @var OrderRow[]  array of OrderRow */
    public $orderRows = array();
    

    /**
     * Adds a shipping fee or invoice fee to the order
     * @param mixed $itemFeeObject  accepts InvoiceFee or ShippingFee object
     * @return $this
     */
    public function addFee($itemFeeObject) {
         if (is_array($itemFeeObject)) {
            foreach ($itemFeeObject as $row) {
                if (get_class($row) == "Svea\ShippingFee") {
                     array_push($this->shippingFeeRows, $row);
                }
                if (get_class($row) == "Svea\InvoiceFee") {
                     array_push($this->invoiceFeeRows, $row);
                }
            }
        } else {
             if (get_class($itemFeeObject) == "Svea\ShippingFee") {
                     array_push($this->shippingFeeRows, $itemFeeObject);
            }
             if (get_class($itemFeeObject) == "Svea\InvoiceFee") {
                 array_push($this->invoiceFeeRows, $itemFeeObject);
            }
        }
        return $this;
    }
    /** @var ShippingFee[]  array of ShippingFee */
    public $shippingFeeRows = array();
    
    /** @var InvoiceFee[]  array of InvoiceFee */
    public $invoiceFeeRows = array();
    
    /**
     * Adds a fixed amount discount or an order total percent discount to the order
     * @param FixedDiscount|RelativeDiscount
     * @return $this
     */
    public function addDiscount($itemDiscountObject) {
        if (is_array($itemDiscountObject)) {
            foreach ($itemDiscountObject as $row) {
                if (get_class($row) == "Svea\FixedDiscount") {
                    array_push($this->fixedDiscountRows, $row);
                }
                if (get_class($row) == "Svea\RelativeDiscount") {
                    array_push($this->relativeDiscountRows, $row);
                }
            }
        }
        else {
            if (get_class($itemDiscountObject) == "Svea\FixedDiscount") {
                array_push($this->fixedDiscountRows, $itemDiscountObject);
            }
            if (get_class($itemDiscountObject) == "Svea\RelativeDiscount") {
                array_push($this->relativeDiscountRows, $itemDiscountObject);
            }
       }
       return $this;
    }
    /** @var FixedDiscount[]  array of FixedDiscount*/
    public $fixedDiscountRows = array();

    /** @var RelativeDiscount[]  array of RelativeDiscount */
    public $relativeDiscountRows = array();
   
    /**
     * @param string Country code as described by ISO 3166-1: "SE", "NO", "DK", "FI", "DE", "NL"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /**
     * Country code as described by ISO 3166-1: "SE", "NO", "DK", "FI","DE", "NL", see http://www.iso.org/iso/country_code for a list.
     * @var string
     */
    public $countryCode;

    /**
     * @param string $currencyAsString in ISO 4217 three-letter format, ex. "SEK", "EUR"
     * @return $this
     */
    public function setCurrency($currencyAsString) {
        $currency = strtoupper( trim($currencyAsString) );
        $this->currency = $currency;
        return $this;
    }
    /**
     * Currency in ISO 4217 three-letter format, ex. "SEK", "EUR"
     * @var string
     */
    public $currency;


    /**
     * @param string $customerReferenceAsString, needs to be unique to the order
     * @return $this
     */
    public function setCustomerReference($customerReferenceAsString) {
        $this->customerReference = $customerReferenceAsString;
        return $this;
    }
    /** @var string your customer Reference number */
    public $customerReference;

    /**
     * @param string $clientOrderNumberAsString
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumberAsString) {
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }
    /** @var string  order number given by client side, should uniquely identify order at client */
    public $clientOrderNumber;

    /**
     * @param string $orderDateAsString  ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @return $this
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }
    /**
     * ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @var string
     */
    public $orderDate;

   /**
     * @internal for testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}
