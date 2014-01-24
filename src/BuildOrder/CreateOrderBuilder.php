<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Continue OrderBuilder by using Create Order functions.
 * End by choosing paymenttype.
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 * @package BuildOrder/CreateOrder
*/
class CreateOrderBuilder {

    /**
     * @var array<OrderRow> Array Rows containing Product rows
     */
    public $orderRows = array();
    /**
     * @var array<ShippingFee> Array ShippingFeeRows containing shippingFee rows
     */
    public $shippingFeeRows = array();
    /**
     * @var array<InvoiceFee> Array InvoiceFeeRows containing invoiceFee rows
     */
    public $invoiceFeeRows = array();
    /**
     * @var testmode. False means in production mode
     */
    public $testmode = false;
    /**
     * @var array<FixedDiscount> Array of FixedDiscountRows from class FixedDiscount
     */
    public $fixedDiscountRows = array();
    /**
     * @var array<RelativeDiscount> Array of RelativeDiscountRows from class RelativeDiscountBuilder
     */
    public $relativeDiscountRows = array();
    /**
    * String recievd by using WebPay::getAddresses($config) function
    * @var type String
    */
    // public $addressSelector;
    /**
     * @var Unique order number from client side
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
     * @var string time, ISO 8601 date
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
     * @type instance of IndividualCustomer or CompanyCustomer
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

     public function addCustomerDetails($itemCustomerObject) {
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }

    /**
     * @param OrderRow $orderRow
     * @return \CreateOrderBuilder
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
     * @return \CreateOrderBuilder
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
     * @param type $itemDiscounObject
     * @return \CreateOrderBuilder
     */
    public function addDiscount($itemDiscounObject) {
        if (is_array($itemDiscounObject)) {
            foreach ($itemDiscounObject as $row) {
                if (get_class($row) == "Svea\FixedDiscount") {
                    array_push($this->fixedDiscountRows, $row);
                }
                if (get_class($row) == "Svea\RelativeDiscount") {
                    array_push($this->relativeDiscountRows, $row);
                }

            }
        }
        else {
            if (get_class($itemDiscounObject) == "Svea\FixedDiscount") {
                array_push($this->fixedDiscountRows, $itemDiscounObject);
            }
            if (get_class($itemDiscounObject) == "Svea\RelativeDiscount") {
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
        $currency = strtoupper( trim($currencyAsString) );
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
    public function setClientOrderNumber($clientOrderNumberAsString) {
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
     * @param type PaymentMethod $paymentMethodAsConst, ex. PaymentMethod::SEB_SE
     * @return \PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst) {
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
