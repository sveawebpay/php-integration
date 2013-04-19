<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Continue OrderBuilder by using Create Order functions.
 * End by choosing paymenttype.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/CreateOrder
*/
class CreateOrderBuilder {

     /**
     * @var Array Rows containing Product rows
     */
    public $orderRows = array();
    /**
     * @var Array ShippingFeeRows containing shippingFee rows
     */
    public $shippingFeeRows = array();
    /**
     * @var  Array InvoiceFeeRows containing invoiceFee rows
     */
    public $invoiceFeeRows = array();
    /**
     * @var testmode. False means in production mode
     */
    public $testmode = false;
    /**
     * @var type Array of FixedDiscountRows from class FixedDiscountBuilder
     */
    public $fixedDiscountRows = array();
    /**
     * @var type Array of RelativeDiscountRows from class RelativeDiscountBuilder
     */
    public $relativeDiscountRows = array();
   /**
    * String recievd by using Webpay::GetAddresses() function
    * @var type String
    */
   // public $addressSelector;
    /**
     * @var Unique order number from client side
     */
    public $clientOrderNumber;
    /*
     * Ex: "SE", "NO", "DK", "FI","DE", "NL"
     * @var type String.
     */
    public $countryCode;
    /**
     * @var type Date time
     */
    public $orderDate;
    /**
     * Ex: "SEK", "EUR"
     * @var type String
     */
    public $currency;
    /**
     * @var Your customer Reference number
     */
    public $customerReference;

    /**
     * @var Instance of class SveaConfig
     */
    public $conf;
    /**
     *
     * @var CustomerIdentity values
     */
    public $customerIdentity;


    /**
     * @param type $orderrows
     */
    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     * When function is called it turns into testmode
     * @return \createOrder

    public function setTestmode() {
        $this->testmode = TRUE;
        return $this;
    }
     *
     * @param type $itemCustomerObject
     * @return \createOrder|\CreateOrderBuilder
     */

     public function addCustomerDetails($itemCustomerObject){
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }
    /**
     * New!
     * @param type $orderRow
     * @return \CreateOrderBuilder
     */
    public function addOrderRow($itemOrderRowObject){
        if(is_array($itemOrderRowObject)){
            foreach ($itemOrderRowObject as $row) {
                array_push($this->orderRows, $row);
            }
        }  else {
             array_push($this->orderRows, $itemOrderRowObject);
        }
       return $this;
    }

    /**
     * New!
     * @param type $itemFeeObject
     * @return \CreateOrderBuilder
     */
    public function addFee($itemFeeObject){
         if(is_array($itemFeeObject)){
            foreach ($itemFeeObject as $row) {
                if (get_class($row) == "ShippingFee") {
                     array_push($this->shippingFeeRows, $row);
                }else{
                     array_push($this->invoiceFeeRows, $row);
                }
            }
        } else {
             if (get_class($itemFeeObject) == "ShippingFee") {
                     array_push($this->shippingFeeRows, $itemFeeObject);
            }else{
                 array_push($this->invoiceFeeRows, $itemFeeObject);
            }
        }

       return $this;
    }
    /**
     * New!
     * @param type $itemDiscounObject
     * @return \CreateOrderBuilder
     */
    public function addDiscount($itemDiscounObject){
         if(is_array($itemDiscounObject)){
            foreach ($itemDiscounObject as $row) {
                if (get_class($row) == "FixedDiscount") {
                     array_push($this->fixedDiscountRows, $row);
                }else{
                     array_push($this->relativeDiscountRows, $row);
                }

            }
        }  else {
             if (get_class($itemDiscounObject) == "FixedDiscount") {
                     array_push($this->fixedDiscountRows, $itemDiscounObject);
            }else{
                 array_push($this->relativeDiscountRows, $itemDiscounObject);
            }
        }
       return $this;
    }


    /**
     * @param type $countryCodeAsString ex. "SE"
     * @return \createOrder
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @param type $currencyAsString ex. "SEK"
     * @return \createOrder
     */
    public function setCurrency($currencyAsString) {
        $currency = trim($currencyAsString);
        $currency = strtoupper($currency);
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param type $customerReferenceAsString ex. "test".rand(0 9999)
     * @return \createOrder
     */
    public function setCustomerReference($customerReferenceAsString) {
        $this->customerReference = $customerReferenceAsString;
        return $this;
    }

    /**
     * @param type $clientOrderNumberAsString
     * @return \createOrder
     */
    public function setClientOrderNumber($clientOrderNumberAsString){
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }

    /**
     * @param type $orderDateAsString ex date('c') eg. 'Y-m-d\TH:i:s\Z'
     * @return \createOrder
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }

    /** Recieve string from getAddresses
     * @param type $addressSelectorAsString
     * @return \createOrder

    public function setAddressSelector($addressSelectorAsString) {
        $this->addressSelector = $addressSelectorAsString;
        return $this;
    }
     *
     */
    /**
     * Start creating cardpayment via PayPage. Returns Paymentform to integrate in shop.
     * @return \HostedPayment
     */
    public function usePayPageCardOnly() {
        return new CardPayment($this);
    }

    /**
     * Start creating direct bank payment via PayPage. Returns Paymentform to integrate in shop.
     * @return \HostedPayment
     */
    public function usePayPageDirectBankOnly() {
        return new DirectPayment($this);
    }

    /**
     * Start creating payment thru paypage. You will be able to customize the PayPage.
     * Returns Paymentform to integrate in shop.
     * @return \PayPagePayment
     */
    public function usePayPage() {
        $paypagepayment = new PayPagePayment($this);
        return $paypagepayment;
    }
    /**
     * Start creating payment with a specific paymentmethod. This function will go directly to the paymentmethod specified.
     * Paymentmethods are found in appendix in our documentation.
     * Returns Paymentform to integrate in shop.
     * @param type PaymentMethod $paymentMethodAsConst, ex. PaymentMethod::DBSEBSE
     * @return \PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst){
        return new PaymentMethodPayment($this, $paymentMethodAsConst);
    }

    /**
     * Start Creating invoicePayment.
     * @return \InvoicePayment
     */
    public function useInvoicePayment() {
        return new InvoicePayment($this);
    }

    /**
     * Start Creating paymentplan payment
     * @param type $campaignCodeAsString
     * @param type $sendAutomaticGiroPaymentFormAsBool optional
     * @return \PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0) {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;
        return new PaymentPlanPayment($this);
    }

   /**
     * For testfunctions
     * @param type $func
     * @return \createOrder
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}