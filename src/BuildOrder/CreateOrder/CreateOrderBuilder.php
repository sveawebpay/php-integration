<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Continue OrderBuilder by using Create Order functions.
 * End by choosing paymenttype.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/CreateOrder
*/
class createOrder {

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
    public $addressSelector;
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
     * @var type Date
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
    public $ssn;
    public $orgNumber;
    public $companyVatNumber;
    public $initials;
    public $email;
    public $phonenumber;
    public $ipAddress;
    public $firstname;
    public $lastname;
    public $street;
    public $housenumber;
    public $zipCode;
    public $coAddress;
    public $locality;
    public $companyName;
    /**
     * @param type $orderrows
     */
    public function __construct() {
        $this->conf = SveaConfig::getConfig();
    }
    
    /**
     * When function is called it turns into testmode
     * @return \createOrder
     */
    public function setTestmode() {
        $this->testmode = TRUE;
        return $this;
    }
    /**
     * Required for private customers in SE, NO, DK, FI
     * @param type $yyyymmddxxxx
     * @return \createOrder
     */
    public function setCustomerSsn($yyyymmddxxxx){
        $this->ssn = $yyyymmddxxxx;
        return $this;
    }
    /**
     * Example: 4608142222
     * Required for company customers in SE, NO, DK, FI
     * For SE: Organisationsnummer
     * For NO: Vatnumber
     * For DK: CVR
     * For FI: Yritystunnus
     * @param type $companyNumberAsInt
     * @return \createOrder
     */
    public function setCustomerCompanyIdNumber($companyIdNumberAsInt){
        $this->orgNumber = $companyIdNumberAsInt;
        return $this;
    }
    /**
     * Example: NL123456789A12
     * @param type $vatNumber
     * Required for NL and DE
     * @return \createOrder
     */
    public function setCustomerCompanyVatNumber($vatNumber){
        $this->companyVatNumber = $vatNumber;
        return $this;
    }
    /**
     * Required for private customers in NL 
     * @param type $initialsAsString
     * @return \createOrder
     */
    public function setCustomerInitials($initialsAsString) {
        $this->initials = $initialsAsString;
        return $this;
    }
    
    /**
     * Required for private customers in NL and DE
     * @param type $yyyy
     * @param type $mm
     * @param type $dd
     * @return \createOrder
     */
    public function setCustomerBirthDate($yyyy, $mm, $dd) {
        if($mm < 10){$mm = "0".$mm; }
        if($dd < 10){$dd = "0".$dd; }
        
        $this->birthDate = $yyyy . $mm . $dd;
        return $this;
    }
    
   /**
     * Optional but desirable
     * @param type $emailAsString
     * @return \createOrder
     */
    public function setCustomerEmail($emailAsString) {
        $this->email = $emailAsString;
        return $this;
    }
     /**
     * Optional
     * @param type $phoneNumberAsInt
     * @return \createOrder
     */
    public function setCustomerPhoneNumber($phoneNumberAsInt) {
        $this->phonenumber = $phoneNumberAsInt;
        return $this;
    }
    /**
     * Optinal but desirable
     * @param type $ipAddressAsString
     * @return \createOrder
     */
    public function setCustomerIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }
    /**
     * Required for private Customers in NL and DE
     * @param type $firstnameAsString
     * @param type $lastnameAsString
     * @return \createOrder
     */
    public function setCustomerName($firstnameAsString, $lastnameAsString) {
        $this->firstname = $firstnameAsString;
        $this->lastname = $lastnameAsString;
        return $this;
    }
    /**
     * Required in NL and DE
     * @param type $streetAsString
     * @param type $houseNumberAsInt
     * @return \createOrder
     */
    public function setCustomerStreetAddress($streetAsString, $houseNumberAsInt) {
        $this->street = $streetAsString;
        $this->housenumber = $houseNumberAsInt;
        return $this;
    }
    /**
     * Optional in NL and DE
     * @param type $coAddressAsString
     * @return \createOrder
     */
    public function setCustomerCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }
    /**
     * Requuired in NL and DE
     * @param type $zipCodeAsString
     * @return \createOrder
     */
    public function setCustomerZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }
    /**
     * Required in NL and DE
     * @param type $cityAsString
     * @return \createOrder
     */
    public function setCustomerLocality($cityAsString) {
        $this->locality = $cityAsString;
        return $this;
    }
    /**
     * Required for Eu countries like NL and DE
     * @param type $nameAsString
     * @return \createOrder
     */
    public function setCustomerCompanyName($nameAsString) {
        $this->companyName = $nameAsString;
        return $this;
    } 
    /**
     * Begin building row for product or other values.
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return \OrderRowBuilder
     */
    public function beginOrderRow() {
        $rowBuilder = new OrderRowBuilder($this);
        array_push($this->orderRows, $rowBuilder);
        return $rowBuilder;
    }
    
    public function addOrderRow($orderRow){
        if(is_array($orderRow)){
            foreach ($orderRow as $row) {
                array_push($this->orderRows, $row);
            }
        }  else {
             array_push($this->orderRows, $orderRow);
        }      
       return $this;
    }
      

    /**
     * Begin building Shipping fee row
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return shippingFeeRows
     */
    public function beginShippingFee() {
        $shippingFeeBuilder = new ShippingFeeBuilder($this);
        array_push($this->shippingFeeRows, $shippingFeeBuilder);
        return $shippingFeeBuilder;
    }

    /**
     * Begin building Invoice fee row
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return \InvoiceFeeBuilder
     */
    public function beginInvoiceFee() {
        $invoiceFeeBuilder = new InvoiceFeeBuilder($this);
        array_push($this->invoiceFeeRows, $invoiceFeeBuilder);
        return $invoiceFeeBuilder;
    }
    
    /**
     * Begin building Discount row for fixed discount
     * @return \FixedDiscountBuilder
     */
    public function beginFixedDiscount() {
        $fixedDiscountRowBuilder = new FixedDiscountBuilder($this);
        array_push($this->fixedDiscountRows, $fixedDiscountRowBuilder);
        return $fixedDiscountRowBuilder;
    }

    /**
     * Begin building Discount row for relative discount
     * @return \RelativeDiscountBuilder
     */
    public function beginRelativeDiscount() {
        $relativeDiscountBuilder = new RelativeDiscountBuilder($this);
        array_push($this->relativeDiscountRows, $relativeDiscountBuilder);
        return $relativeDiscountBuilder;
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
     */
    public function setAddressSelector($addressSelectorAsString) {
        $this->addressSelector = $addressSelectorAsString;
        return $this;
    }
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

?>
