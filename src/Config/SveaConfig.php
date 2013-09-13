<?php
namespace Svea;
/**
 * Class contains Merchant identification values for Requests to external Services
 * Options:
 * 1. File can manually be changed an will be used by integration package
 * 2. Use methods in php-integration package api to set values
 * @package Config
 */
class SveaConfig {

    const SWP_TEST_URL = "https://test.sveaekonomi.se/webpay/payment";
    const SWP_PROD_URL = "https://webpay.sveaekonomi.se/webpay/payment";
    const SWP_TEST_WS_URL = "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL";
    const SWP_PROD_WS_URL = "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL";
    const SWP_TEST_HOSTED_ADMIN_URL = "https://test.sveaekonomi.se/webpay/rest/";
    const SWP_PROD_HOSTED_ADMIN_URL = "https://test.sveaekonomi.se/webpay/rest/";

    public static function getDefaultConfig() {
        return self::getTestConfig();
    }

    public static function getProdConfig() {
        $prodConfig = array();
        $prodConfig["SE"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password"  => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password"  => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"    => "")
                                    )
                                );
        $prodConfig["NO"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password"  => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password"  => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"    => "")
                                    )
                                );
        $prodConfig["FI"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"   => "")
                                    )
                                );
        $prodConfig["DK"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"   => "")
                                    )
                                );
        $prodConfig["NL"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"   => "")
                                    )
                                );
        $prodConfig["DE"] = array("auth" =>
                               array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "", "password" => "", "clientNumber" => ""),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => "", "secret"   => "")
                                    )
                                );
        $url =              array(
                                \ConfigurationProvider::HOSTED_TYPE      => self::SWP_PROD_URL,
                                \ConfigurationProvider::INVOICE_TYPE     => self::SWP_PROD_WS_URL,
                                \ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_PROD_WS_URL
                            );

        return new SveaConfigurationProvider(array("url" => $url, "credentials" => $prodConfig));
    }

    public static function getTestConfig() {
        $testConfig = array();
         $testConfig["SE"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "sverigetest", "password" => "sverigetest", "clientNumber" => 79021),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "sverigetest", "password" => "sverigetest", "clientNumber" => 59999),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => 1130, "secret" => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["NO"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"     => "norgetest2", "password" => "norgetest2", "clientNumber"=> "33308"),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"     => "norgetest2", "password" => "norgetest2", "clientNumber"=> "32503"),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId"   => 1130, "secret" => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["FI"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"     => "finlandtest2", "password"    => "finlandtest2", "clientNumber"    => "26136"),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"     => "finlandtest2", "password"    => "finlandtest2", "clientNumber"    => "27136"),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId"   => 1130, "secret"    => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["DK"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "danmarktest2", "password" => "danmarktest2", "clientNumber" => "62008"),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "danmarktest2", "password" => "danmarktest2", "clientNumber" => "64008"),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => 1130, "secret" => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["NL"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "hollandtest", "password" => "hollandtest", "clientNumber" => 85997),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "hollandtest", "password" => "hollandtest", "clientNumber" => 86997),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => 1130, "secret" => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
        $testConfig["DE"] = array("auth" =>
                                array(
                                    \ConfigurationProvider::INVOICE_TYPE     => array("username"   => "germanytest", "password" => "germanytest", "clientNumber" => 14997),
                                    \ConfigurationProvider::PAYMENTPLAN_TYPE => array("username"   => "germanytest", "password" => "germanytest", "clientNumber" => 16997),
                                    \ConfigurationProvider::HOSTED_TYPE      => array("merchantId" => 1130, "secret" => "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3")
                                    )
                                );
         $url =             array(
                                \ConfigurationProvider::HOSTED_TYPE      => self::SWP_TEST_URL,
                                \ConfigurationProvider::INVOICE_TYPE     => self::SWP_TEST_WS_URL,
                                \ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_TEST_WS_URL,
                                \ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_TEST_HOSTED_ADMIN_URL
                            );

        return new SveaConfigurationProvider(array("url" => $url, "credentials" => $testConfig));
    }
}
