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
        $this->merchantId = 1175;
        $this->secret = "d153477288051d6001adf0648405e0fcfaa3ee2a8dc90dd3151341a1d68b1a4388616585fe7bc15cd06882070b0d92aa92de6cde1e7a21dc7e65e81cee6af43f";
       
        /**
        $this->merchantId = 1200;
        $this->secret = "27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d";
         * 
         */
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
