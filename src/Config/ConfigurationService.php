<?php

namespace Svea\WebPay\Config;

/**
 * ConfigurationService provide configuration parameters for a given environment
 * config_prod.php and config_test files contain the authorization credentials used in requests
 * to Svea services. It provide credential data to SveaConfigurationProvider.
 * You can change that params in config files.
 */
class ConfigurationService
{
    const SWP_TEST_URL = "https://test.sveaekonomi.se/webpay/payment";
    const SWP_PROD_URL = "https://webpay.sveaekonomi.se/webpay/payment";
    const SWP_TEST_WS_URL = "https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL";
    const SWP_PROD_WS_URL = "https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL";
    const SWP_TEST_HOSTED_ADMIN_URL = "https://test.sveaekonomi.se/webpay/rest/"; // ends with "/" as we need to add request method
    const SWP_PROD_HOSTED_ADMIN_URL = "https://webpay.sveaekonomi.se/webpay/rest/"; // ends with "/" as we need to add request method

    const SWP_TEST_ADMIN_URL = "https://partnerweb.sveaekonomi.se/WebPayAdminService_test/AdminService.svc/backward"; // /backward => SOAP 1.1
    const SWP_PROD_ADMIN_URL = "https://partnerweb.sveaekonomi.se/WebPayAdminService/AdminService.svc/backward"; // /backward => SOAP 1.1

    const SWP_TEST_PREPARED_URL = "https://test.sveaekonomi.se/webpay/preparedpayment/";
    const SWP_PROD_PREPARED_URL = "https://webpay.sveaekonomi.se/webpay/preparedpayment/";


    /**
     * @return SveaConfigurationProvider
     */
    public static function getProdConfig()
    {
        return self::getConfig(true);
    }

    /**
     * @return SveaConfigurationProvider
     */
    public static function getDefaultConfig()
    {
        return self::getTestConfig();
    }

    /**
     * Replace the provided Svea test account credentials with your own to use
     * the package with your own account.
     *
     * @return SveaConfigurationProvider
     */
    public static function getTestConfig()
    {
        return self::getConfig(false);
    }

    /**
     * getSingleCountryConfig() may be used to provide a specific single configuration to use in i.e. the package test suite.
     *
     * Provide the following credentials as parameters. If a value is set to null, a default value will be provided instead.
     *
     * @param string $countryCode
     * @param string $invoiceUsername
     * @param string $invoicePassword
     * @param string $invoiceClientNo
     * @param string $paymentPlanUsername
     * @param string $paymentPlanPassword
     * @param string $paymentPlanClientNo
     * @param string $merchantId
     * @param string $secret
     * @param bool $prod
     * @return SveaConfigurationProvider
     */
    public static function getSingleCountryConfig(
        $countryCode, $invoiceUsername, $invoicePassword, $invoiceClientNo,
        $paymentPlanUsername, $paymentPlanPassword, $paymentPlanClientNo,
        $merchantId, $secret, $prod)
    {
        $prod = ($prod == null) ? false : $prod;

        list($config, $urls) = self::retrieveConfigFile($prod);

        $defaultConfig = $config['credentials'];
        $defaultMerchantId = $config['commonCredentials']['merchantId'];
        $defaultSecretWord = $config['commonCredentials']['secret'];
        $defaultCountryCode = $config['defaultCountryCode'];
        $integrationProperties = $config['integrationParams'];

        $countryCode = ($countryCode == null) ? $defaultCountryCode : $countryCode;
        $configPerCountry = $defaultConfig[$countryCode];

        $configPerInvoiceType = $configPerCountry[ConfigurationProvider::INVOICE_TYPE];
        $invoiceUsername = ($invoiceUsername == null) ? $configPerInvoiceType['username'] : $invoiceUsername;
        $invoicePassword = ($invoicePassword == null) ? $configPerInvoiceType['password'] : $invoicePassword;
        $invoiceClientNo = ($invoiceClientNo == null) ? $configPerInvoiceType['clientNumber'] : $invoiceClientNo;

        $configPerPPType = $configPerCountry[ConfigurationProvider::PAYMENTPLAN_TYPE];
        $paymentPlanUsername = ($paymentPlanUsername == null) ? $configPerPPType['username'] : $paymentPlanUsername;
        $paymentPlanPassword = ($paymentPlanPassword == null) ? $configPerPPType['password'] : $paymentPlanPassword;
        $paymentPlanClientNo = ($paymentPlanClientNo == null) ? $configPerPPType['clientNumber'] : $paymentPlanClientNo;

        $merchantId = ($merchantId == null) ? $defaultMerchantId : $merchantId;
        $secret = ($secret == null) ? $defaultSecretWord : $secret;

        // set up credentials array for given country:
        $singleCountryConfig[$countryCode] = array("auth" =>
            array(
                ConfigurationProvider::INVOICE_TYPE => array(
                    "username" => $invoiceUsername,
                    "password" => $invoicePassword,
                    "clientNumber" => $invoiceClientNo
                ),
                ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                    "username" => $paymentPlanUsername,
                    "password" => $paymentPlanPassword,
                    "clientNumber" => $paymentPlanClientNo
                ),
                ConfigurationProvider::HOSTED_TYPE => array(
                    "merchantId" => $merchantId,
                    "secret" => $secret
                )
            )
        );

        $singleCountryConfig['common']['auth'][ConfigurationProvider::HOSTED_TYPE] = array(
            "merchantId" => $merchantId,
            "secret" => $secret
        );

        // return a ConfigurationProvider object
        return new SveaConfigurationProvider(
            array("url" => $urls, "credentials" => $singleCountryConfig, "integrationproperties" => $integrationProperties)
        );
    }

    private static function getConfig($isProd)
    {
        list($config, $urls) = self::retrieveConfigFile($isProd);

        $credentialParams = array();
        $credentials = $config['credentials'];

        $commonCredentials = $config['commonCredentials'];
        $credentialParams['common'] = array();
        foreach ($credentials as $countryCode => $configPerCountry) {
            $credentialParams[$countryCode] = array('auth' => array());
            foreach ($configPerCountry as $paymentType => $configPerType) {
                $credentialParams[$countryCode]['auth'][$paymentType] = $configPerType;
            }
            $credentialParams[$countryCode]['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;
        }

        $credentialParams['common']['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;

        $integrationProperties = $config['integrationParams'];

        return new SveaConfigurationProvider(array("url" => $urls, "credentials" => $credentialParams, "integrationproperties" => $integrationProperties));
    }

    private static function retrieveConfigFile($isProd)
    {
        if ($isProd === true) {
            $config = require 'config_prod.php';
            $urls = self::getProdUrls();
        } else {
            $config = require 'config_test.php';
            $urls = self::getTestUrls();
        }

        return array($config, $urls);
    }

    private static function getTestUrls()
    {
        return array(
            ConfigurationProvider::HOSTED_TYPE => self::SWP_TEST_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_TEST_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_TEST_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_TEST_PREPARED_URL
        );
    }

    private static function getProdUrls()
    {
        return array(
            ConfigurationProvider::HOSTED_TYPE => self::SWP_PROD_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_PROD_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_PROD_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_PROD_PREPARED_URL
        );
    }
}
