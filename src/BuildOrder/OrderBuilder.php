<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * OrderBuilder collects and prepares order data to be sent to Svea. It is the
 * parent of CreateOrderBuilder and DeliverOrderBuilder.
 *
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea WebPay
 */
class OrderBuilder {

    /** @var boolean  true indicates test mode, false indicates production mode */
    public $testmode = false;

    /** @var \ConfigurationProvider $conf */
    public $conf;

    /** @var \Svea\IndividualCustomer|\Svea\CompanyCustomer */
    public $customerIdentity;

    /** @var \Svea\OrderRow []  array of OrderRow */
    public $orderRows = array();

    /** @var \Svea\ShippingFee []  array of ShippingFee */
    public $shippingFeeRows = array();

    /** @var \Svea\InvoiceFee []  array of InvoiceFee */
    public $invoiceFeeRows = array();

    /** @var \Svea\FixedDiscount []  array of FixedDiscount*/
    public $fixedDiscountRows = array();

    /** @var \Svea\RelativeDiscount []  array of RelativeDiscount */
    public $relativeDiscountRows = array();

    /** @var type array of all rows in the order they are set */
    public $rows = array();

    /** @var string Country code as described by ISO 3166-1: "SE", "NO", "DK", "FI","DE", "NL" */
    public $countryCode;

    /** @var string Currency in ISO 4217 three-letter format, ex. "SEK", "EUR" */
    public $currency;

    /** @var string ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12" */
    public $orderDate;

    /** @var string your customer Reference number */
    public $customerReference;

    /** @var string order number given by client side, should uniquely identify order at client */
    public $clientOrderNumber;

    /**
     * @param \ConfigurationProvider $config 
     */
    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     * Required for invoice and payment plan orders - add customer information to the order
     * Optional for card and direct bank orders
     *
     * See the customer objects for information on required customer information fields for
     * invoice and payment plan orders.
     *
     * @see \Svea\IndividualCustomer \Svea\IndividualCustomer
     * @see \Svea\CompanyCustomer \Svea\CompanyCustomer
     *
     * @param \Svea\IndividualCustomer|\Svea\CompanyCustomer $itemCustomerObject
     * @return $this
     */
     public function addCustomerDetails($itemCustomerObject) {
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }

    /**
     * Required - you need to add at least one order row to the order
     *
     * @param \Svea\OrderRow $itemOrderRowObject
     * @return $this
     */
    public function addOrderRow($itemOrderRowObject) {
        if (is_array($itemOrderRowObject)) {
            foreach ($itemOrderRowObject as $row) {
                array_push($this->orderRows, $row);
                array_push($this->rows, $row);
            }
        } else {
             array_push($this->orderRows, $itemOrderRowObject);
             array_push($this->rows, $itemOrderRowObject);
        }
       return $this;
    }

    /**
     * Optional - adds a shipping fee or invoice fee to the order
     *
     * @param \Svea\InvoiceFee|\Svea\ShippingFee $itemFeeObject
     * @return $this
     */
    public function addFee($itemFeeObject) {
         if (is_array($itemFeeObject)) {
            foreach ($itemFeeObject as $row) {
                array_push($this->rows, $row);
                if (get_class($row) == "Svea\ShippingFee") {
                    array_push($this->shippingFeeRows, $row);
                }
                if (get_class($row) == "Svea\InvoiceFee") {
                    array_push($this->invoiceFeeRows, $row);
                }
            }
        } else {
             array_push($this->rows, $itemFeeObject);
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
     * Optional - adds a fixed amount discount or an order total percent discount to the order
     *
     * See the discount objects for information on how the discount is calculated et al.
     *
     * @see \Svea\FixedDiscount \Svea\FixedDiscount
     * @see \Svea\RelativeDiscount \Svea\RelativeDiscount
     *
     * @param \Svea\FixedDiscount|\Svea\RelativeDiscount $itemDiscountObject
     * @return $this
     */
    public function addDiscount($itemDiscountObject) {
        if (is_array($itemDiscountObject)) {
            foreach ($itemDiscountObject as $row) {
                array_push($this->rows, $row);
                if (get_class($row) == "Svea\FixedDiscount") {
                    array_push($this->fixedDiscountRows, $row);
                }
                if (get_class($row) == "Svea\RelativeDiscount") {
                    array_push($this->relativeDiscountRows, $row);
                }
            }
        }
        else {
             array_push($this->rows, $itemDiscountObject);
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
     * Required - set order country code, we recommend basing this on the customer billing address
     *
     * For orders using the invoice or payment plan payment methods, you need to supply a country code that corresponds
     * to the account credentials used for the address lookup. (Note that this means that these methods don't support
     * orders from foreign countries, this is a consequence of the fact that the invoice and payment plan payment
     * methods don't support foreign orders.)
     *
     * @param string $countryCodeAsString Country code as described by ISO 3166-1, one of "SE", "NO", "DK", "FI", "DE", "NL"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required for card payment, direct bank & PayPage payments. Ignored for invoice and payment plan.
     *
     * Ignored for invoice and payment plan orders, which use the selected client id currency, as determined by ConfigurationProvider and setCountryCode.
     *
     * @param string $currencyAsString in ISO 4217 three-letter format, ex. "SEK", "EUR"
     * @return $this
     */
    public function setCurrency($currencyAsString) {
        $currency = strtoupper( trim($currencyAsString) );
        $this->currency = $currency;
        return $this;
    }

    /**
     * Optional - set a client side customer reference, i.e. customer number etc.
     * Max length 30 characters.
     *
     * @param string  $customerReferenceAsString needs to be unique to the order for card and direct bank orders
     * @return $this
     */
    public function setCustomerReference($customerReferenceAsString) {
        $this->customerReference = $customerReferenceAsString;
        return $this;
    }

    /**
     * Required for Card, Direct Bank and PaymentMethod and PayPage orders - set a client side order identifier, i.e. the webshop order number etc.
     * Max length 30 characters.
     *
     * Note that for Card and Direct Bank orders, you may not reuse a previously sent client order number, or you'll get error 127 from the service.
     *
     * @param string  $clientOrderNumberAsString
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumberAsString) {
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }

    /**
     * Required for Invoice and Payment plan orders -- set the order date
     *
     * @param string $orderDateAsString  ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @return $this
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }
}
