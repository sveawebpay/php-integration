<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CreateOrderBuilder collects and prepares order data to be sent to Svea.
 * 
 * Set all required order attributes in CreateOrderBuilder instance by using the 
 * instance setXXX() methods. Instance methods can be chained together, as they 
 * return the instance itself.
 * 
 * Finish setting order attributes by chosing a payment method with the useXXX() 
 * paymenttype.
 * 
 * @param $config 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class CreateOrderBuilder {
    /**
     * @var array<OrderRow> Array Rows containing Product rows
     */
    public $orderRows = array();
    /**
     * @attrib array<ShippingFee> Array ShippingFeeRows containing shippingFee rows
     */
    public $shippingFeeRows = array();
    /**
     * @attrib array<InvoiceFee> Array InvoiceFeeRows containing invoiceFee rows
     */
    public $invoiceFeeRows = array();
    /**
     * @attrib testmode. False means in production mode
     */
    public $testmode = false;
    /**
     * @attrib array<FixedDiscount> Array of FixedDiscountRows from class FixedDiscount
     */
    public $fixedDiscountRows = array();
    /**
     * @attrib array<RelativeDiscount> Array of RelativeDiscountRows from class RelativeDiscountBuilder
     */
    public $relativeDiscountRows = array();
    /**
    * String recievd by using WebPay::getAddresses($config) function
    * @var type String
    */
    // public $addressSelector;
   
    /**
     * @attrib order number given by client side, should uniquely identify order at client
     */
    public $clientOrderNumber;

    /**
     * Country code as described by Iso 3166-1 (alpha-2), see http://www.iso.org/iso/country_code for a list.
     * Ex: "SE", "NO", "DK", "FI","DE", "NL"
     * @var type String.
     */
    public $countryCode;

    /**
     * ISO 8601 date, as produced by php date('c'): 2004-02-12T15:19:21+00:00, also accepts date in format "2004-02-12"
     * @attrib string time, ISO 8601 date
     */
    public $orderDate;

    /**
     * Ex: "SEK", "EUR"
     * @attrib type String
     */
    public $currency;

    /**
     * @attrib Your customer Reference number
     */
    public $customerReference;

    /**
     * @attrib Instance of class SveaConfig
     */

    public $conf;
    /**
     * @attrib mixed -- instance of IndividualCustomer or CompanyCustomer
     */
    public $customerIdentity;

    /**
     * @param type $orderrows
     */
    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     *
     * @param type $itemCustomerObject
     * @return $this
     */

     public function addCustomerDetails($itemCustomerObject) {
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }

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

    /**
     * @param type $itemFeeObject
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

    /**
     * @param mixed $itemDiscountObject instance of either Svea\FixedDiscount or Svea\RelativeDiscount
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

    /**
     * @param type $countryCodeAsString ex. "SE"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @param string $currencyAsString ex. "SEK"
     * @return $this
     */
    public function setCurrency($currencyAsString) {
        $currency = strtoupper( trim($currencyAsString) );
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param string $customerReferenceAsString ex. "test".rand(0 9999)
     * @return $this
     */
    public function setCustomerReference($customerReferenceAsString) {
        $this->customerReference = $customerReferenceAsString;
        return $this;
    }

    /**
     * @param string $clientOrderNumberAsString
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumberAsString) {
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }

    /**
     * @param string $orderDateAsString ex date('c') eg. 'Y-m-d\TH:i:s\Z'
     * @return $this
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }

    /**
     * Start creating cardpayment via PayPage. Returns Paymentform to integrate in shop.
     * @return Svea\HostedPayment
     */
    public function usePayPageCardOnly() {
        return new CardPayment($this);
    }

    /**
     * Start creating direct bank payment via PayPage. Returns Paymentform to integrate in shop.
     * @return Svea\HostedPayment
     */
    public function usePayPageDirectBankOnly() {
        return new DirectPayment($this);
    }

    /**
     * Start creating payment thru paypage. You will be able to customize the PayPage.
     * Returns Paymentform to integrate in shop.
     * @return Svea\PayPagePayment
     */
    public function usePayPage() {
        $paypagepayment = new PayPagePayment($this);
        return $paypagepayment;
    }

    /**
     * Start creating payment with a specific paymentmethod. This function will go directly to the paymentmethod specified.
     * Paymentmethods are found in appendix in our documentation.
     * Returns Paymentform to integrate in shop.
     * @param string PaymentMethod $paymentMethodAsConst, ex. PaymentMethod::SEB_SE
     * @return Svea\PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst) {
        return new PaymentMethodPayment($this, $paymentMethodAsConst);
    }

    /**
     * Start Creating invoicePayment.
     * @return Svea\InvoicePayment
     */
    public function useInvoicePayment() {
        return new InvoicePayment($this);
    }

    /**
     * Start Creating paymentplan payment
     * @param string $campaignCodeAsString
     * @param boolean $sendAutomaticGiroPaymentFormAsBool optional
     * @return Svea\PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0) {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;
        return new PaymentPlanPayment($this);
    }

   /**
     * For testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}
