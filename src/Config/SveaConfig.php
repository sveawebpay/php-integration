<?php
/**
 * Class contains Merchant identification values for Requests to external Services
 * Options:
 * 1. File can manually be changed an will be used by integration package
 * 2. Use methods in php-integration package api to set values
 * @package Config
 */
class SveaConfig {

    public $username;
    public $password;
    public $invoiceClientnumber;
    public $paymentPlanClientnumber;
    public $merchantId;
    public $secret;

    const SWP_TEST_URL = "https://test.sveaekonomi.se/webpay/payment";
    const SWP_PROD_URL = "https://webpay.sveaekonomi.se/webpay/payment";
    const SWP_TEST_WS_URL = "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL";
    const SWP_PROD_WS_URL = "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL";

    /**
     * Sets default testing values.
     * Change manually to your merchant identification values.
     */
    public function __construct() {
        $this->username = 'sverigetest';
        $this->password = 'sverigetest';
        $this->invoiceClientnumber = 79021;
        $this->paymentPlanClientnumber = 59999;       
        $this->merchantId = 1130;
        $this->secret = "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3";
      
    }

    /**
     * 
     * @param type $type
     * @return type Array
     */
    public function getPasswordBasedAuthorization($type) {
        $auth['username'] = $this->username;
        $auth['password'] = $this->password;
        if ($type == 'PaymentPlan') {
            $auth['clientnumber'] = $this->paymentPlanClientnumber;
        } else {
            $auth['clientnumber'] = $this->invoiceClientnumber;
        }
        return $auth;
    }

    public function getMerchantIdBasedAuthorization() {
        return array($this->merchantId, $this->secret);
    }
    
    /**
     * Get an instance of the Config
     * @return SveaConfig
     */
    public static function getConfig() {
        return new SveaConfig();
    }
}

?>
