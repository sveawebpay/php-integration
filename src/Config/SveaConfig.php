<?php
/**
 * Class contains Merchant identification values for Requests to external Services
 * Options:
 * 1. File can manually be changed an will be used by integration package
 * 2. Use methods in php-integration package api to set values
 * @package Config
 */
class SveaConfig {
/**
    const SWP_TEST_URL = "https://test.sveaekonomi.se/webpay/payment";
    const SWP_PROD_URL = "https://webpay.sveaekonomi.se/webpay/payment";
    const SWP_TEST_WS_URL = "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL";
    const SWP_PROD_WS_URL = "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL";
 * 
 */

    /**
     * Sets default testing values.
     * Change manually to your merchant identification values.
     */
    public function __construct() {
        /**
        $this->username = 'sverigetest';
        $this->password = 'sverigetest';
        $this->invoiceClientnumber = 79021;
        $this->paymentPlanClientnumber = 59999;       
        $this->merchantId = 1130;
        $this->secret = "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3";
      
         * 
         */
    }

    /**
     * 
     * @param type $type
     * @return type Array
     
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
     * 
     * @return \type
     

    public function getmerchantIdBasedAuthorization() {
        return array($this->merchantId, $this->secret);
    }
     * 
     */
    
    /**
     * Get an instance of the Config
     * @return SveaConfig
     
    public static function getConfig() {
        return new SveaConfig();
    }
     * 
     * @return \SveaConfig
     */

    public static function getDefaultConfig() {
        return self::getTestConfig();
    }

    public static function getProdConfig() {
        $prodConfig = array();
        $prodConfig["SE"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     =>"", "password"  => "", "clientNumber"    =>""),
                                    "PAYMENTPLAN"   => array("username"     =>"", "password"  => "", "clientNumber"    =>""),
                                    "HOSTED"        => array("merchantId"   =>"", "secret"   => "")
                                    )
                                );
        $prodConfig["NO"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     =>"", "password"  => "", "clientNumber"    =>""),
                                    "PAYMENTPLAN"   => array("username"     =>"", "password"  => "", "clientNumber"    =>""),
                                    "HOSTED"        => array("merchantId"   =>"", "secret"   => "")
                                    )
                                );
        $prodConfig["FI"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "PAYMENTPLAN"   => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "HOSTED"        => array("merchantId"   => "", "secret"     => "")
                                    )
                                );
        $prodConfig["DK"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "PAYMENTPLAN"   => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "HOSTED"        => array("merchantId"   => "", "secret"     => "")
                                    )
                                );
        $prodConfig["NL"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "PAYMENTPLAN"   => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "HOSTED"        => array("merchantId"   => "", "secret"     => "")
                                    )
                                );
        $prodConfig["DE"] = array("auth" =>  
                               array(
                                    "INVOICE"       => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "PAYMENTPLAN"   => array("username"     => "", "password"   => "", "clientNumber"    => ""),
                                    "HOSTED"        => array("merchantId"   => "", "secret"     => "")
                                    )
                                );
        $url =              array(
                                "HOSTED"            =>      "https://webpay.sveaekonomi.se/webpay/payment",
                                "INVOICE"           =>      "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL",
                                "PAYMENTPLAN"       =>      "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL"
                            );
        
        return new SveaConfigurationProvider(array("url" => $url, "credentials" => $prodConfig));
    }
    
    public static function getTestConfig() {
        $testConfig = array();
         $testConfig["SE"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "sverigetest", "password"    => "sverigetest", "clientNumber"    => 79021),
                                    "PAYMENTPLAN"   => array("username"     => "sverigetest", "password"    => "sverigetest", "clientNumber"    => 59999),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["NO"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "webpay_test_no", "password" => "dvn349hvs9+29hvs", "clientNumber"=> 32666),
                                    "PAYMENTPLAN"   => array("username"     => "webpay_test_no", "password" => "dvn349hvs9+29hvs", "clientNumber"=> 36000),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["FI"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "finlandtest", "password"    => "finlandtest", "clientNumber"    => 29995),
                                    "PAYMENTPLAN"   => array("username"     => "finlandtest", "password"    => "finlandtest", "clientNumber"    => 29992),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["DK"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "danmarktest", "password"    => "danmarktest", "clientNumber"    => 60006),
                                    "PAYMENTPLAN"   => array("username"     => "danmarktest", "password"    => "danmarktest", "clientNumber"    => 60004),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["NL"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "hollandtest", "password"    => "hollandtest", "clientNumber"    => 85997),
                                    "PAYMENTPLAN"   => array("username"     => "hollandtest", "password"    => "hollandtest", "clientNumber"    => 86997),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["DE"] = array("auth" =>  
                                array(
                                    "INVOICE"       => array("username"     => "germanytest", "password"    => "germanytest", "clientNumber"    => 14997),
                                    "PAYMENTPLAN"   => array("username"     => "germanytest", "password"    => "germanytest", "clientNumber"    => 16997),
                                    "HOSTED"        => array("merchantId"   => 1130, "secret"               => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
         $url =              array(
                                "HOSTED"            =>      "https://test.sveaekonomi.se/webpay/payment",
                                "INVOICE"           =>      "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL",
                                "PAYMENTPLAN"       =>      "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL"
                            );

        return new SveaConfigurationProvider(array("url" => $url, "credentials" => $testConfig));
    }
}

?>
