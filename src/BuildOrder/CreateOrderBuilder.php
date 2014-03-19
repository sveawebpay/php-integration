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
    /** @var OrderRow[] */
    public $orderRows = array();
    
    /** @var ShippingFee[] */
    public $shippingFeeRows = array();
    
    /** @var InvoiceFee[] */
    public $invoiceFeeRows = array();
    
    /** @var boolean False means in production mode */
    public $testmode = false;
    
    /** @var FixedDiscount[] */
    public $fixedDiscountRows = array();

    /** @var RelativeDiscount[] */
    public $relativeDiscountRows = array();
   
    /** @var string order number given by client side, should uniquely identify order at client */
    public $clientOrderNumber;

    /**
     * Country code as described by Iso 3166-1: "SE", "NO", "DK", "FI","DE", "NL", see http://www.iso.org/iso/country_code for a list.
     * @var string
     */
    public $countryCode;

    /**
     * ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @var string
     */
    public $orderDate;

    /**
     * Currency in three-letter format Ex: "SEK", "EUR"        
     * @todo TODO lookup ISO currency 
     * @var string
     */
    public $currency;

    /** @var string your customer Reference number */
    public $customerReference;

    /** @var type */
    public $conf;

    /** @var IndividualCustomer|CompanyCustomer */
    public $customerIdentity;

    /** @param type $orderrows */
    public function __construct($config) {
        $this->conf = $config;
    }

    /**
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
     * Adds a shipping fee or invoice fee to the order
     * @param InvoiceFee|ShippingFee
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

    /**
     * @param string Country code as described by Iso 3166-1: "SE", "NO", "DK", "FI", "DE", "NL"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @param string $currencyAsString ex. "SEK"
     * @TODO TODO look up ISO standard of currency
     * @return $this
     */
    public function setCurrency($currencyAsString) {
        $currency = strtoupper( trim($currencyAsString) );
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param string $customerReferenceAsString, needs to be unique to the order
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
     * @param string $orderDateAsString  ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @return $this
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }

    /**
     * Use usePayPageCardOnly to initate a card payment via PayPage. 
     * 
     * Set additional attributes using CardPayment methods.
     * @return CardPayment
     */
    public function usePayPageCardOnly() {
        return new CardPayment($this);
    }

    /**
     * Use usePayPageDirectBankOnly to initate a direct bank payment via PayPage. 
     * 
     * Set additional attributes using DirectPayment methods.
     * @return DirectPayment
     */
    public function usePayPageDirectBankOnly() {
        return new DirectPayment($this);
    }

    /**
     * Use usePayPage to initate a payment via PayPage. 
     * 
     * Set additional attributes using PayPagePayment methods.
     * @return PayPagePayment
     */
    public function usePayPage() {
        $paypagepayment = new PayPagePayment($this);
        return $paypagepayment;
    }

    /**
     * Use usePayPage to initate a payment via PayPage, going straight to the payment method specified. 
     * 
     * Set additional attributes using PayPagePayment methods.
     * Paymentmethods are found in appendix in our documentation and are available in the PaymentMethod class.
     * @see PaymentMethod class
     * @param string $paymentMethodAsConst  i.e. PaymentMethod::SEB_SE et al
     * @return PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst) {
        return new PaymentMethodPayment($this, $paymentMethodAsConst);
    }

    /**
     * Use useInvoicePayment to initate an invoice payment. Set additional attributes using InvoicePayment methods.
     * @return InvoicePayment
     */
    public function useInvoicePayment() {
        return new InvoicePayment($this);
    }

    /**
     * Use usePaymentPlanPayment to initate an invoice payment. Set additional attributes using PaymentPlanPayment methods.
     * @param string $campaignCodeAsString
     * @param boolean $sendAutomaticGiroPaymentFormAsBool (optional)
     * @return PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0) {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;
        return new PaymentPlanPayment($this);
    }

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
