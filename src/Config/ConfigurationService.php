<?php

namespace Svea\WebPay\Config;

use Svea\Checkout\Transport\Connector;

/**
 * ConfigurationService provide configuration parameters for a given environment
 * config_prod.php and config_test files contain the authorization credentials used in requests
 * to Svea services. It provide credential data to SveaConfigurationProvider.
 * You can change that params in config files.
 */
class ConfigurationService
{
    const SWP_TEST_URL = "https://webpaypaymentgatewaystage.svea.com/webpay/payment";
    const SWP_PROD_URL = "https://webpaypaymentgateway.svea.com/webpay/payment";
    const SWP_TEST_WS_URL = "https://webpaywsstage.svea.com/SveaWebPay.asmx?WSDL";
    const SWP_PROD_WS_URL = "https://webpayws.svea.com/SveaWebPay.asmx?WSDL";
    const SWP_TEST_HOSTED_ADMIN_URL = "https://webpaypaymentgatewaystage.svea.com/webpay/rest/"; // ends with "/" as we need to add request method
    const SWP_PROD_HOSTED_ADMIN_URL = "https://webpaypaymentgateway.svea.com/webpay/rest/"; // ends with "/" as we need to add request method

    const SWP_TEST_ADMIN_URL = "https://webpayadminservicestage.svea.com/AdminService.svc/backward";
    const SWP_PROD_ADMIN_URL = "https://webpayadminservice.svea.com/AdminService.svc/backward";

    const SWP_TEST_PREPARED_URL = "https://webpaypaymentgatewaystage.svea.com/webpay/preparedpayment/";
    const SWP_PROD_PREPARED_URL = "https://webpaypaymentgateway.svea.com/webpay/preparedpayment/";

    const CHECKOUT_TEST_BASE_URL = Connector::TEST_BASE_URL;
    const CHECKOUT_PROD_BASE_URL = Connector::PROD_BASE_URL;

    const CHECKOUT_ADMIN_TEST_BASE_URL = Connector::TEST_ADMIN_BASE_URL;
    const CHECKOUT_ADMIN_PROD_BASE_URL = Connector::PROD_ADMIN_BASE_URL;


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
     * @param string $accountCreditUsername
     * @param string $accountCreditPassword
     * @param string $accountCreditClientNo
     * @param string $merchantId
     * @param string $secret
     * @param bool $prod
     * @return SveaConfigurationProvider
     */
    public static function getSingleCountryConfig(
        $countryCode,
        $invoiceUsername, $invoicePassword, $invoiceClientNo,
        $paymentPlanUsername, $paymentPlanPassword, $paymentPlanClientNo,
        $accountCreditUsername, $accountCreditPassword, $accountCreditClientNo,
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

        $configPerAccCredType = $configPerCountry[ConfigurationProvider::ACCOUNTCREDIT_TYPE];
        $accountCreditUsername = ($accountCreditUsername == null) ? $configPerAccCredType['username'] : $accountCreditUsername;
        $accountCreditPassword = ($accountCreditPassword == null) ? $configPerAccCredType['password'] : $accountCreditPassword;
        $accountCreditClientNo = ($accountCreditClientNo == null) ? $configPerAccCredType['clientNumber'] : $accountCreditClientNo;

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
                ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                    "username" => $accountCreditUsername,
                    "password" => $accountCreditPassword,
                    "clientNumber" => $accountCreditClientNo
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

        $singleCountryConfig['common']['auth'][ConfigurationProvider::CHECKOUT] = array(
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
        $checkoutCredentials = $config['checkoutCredentials'];
        $credentialParams['common'] = array();
        foreach ($credentials as $countryCode => $configPerCountry) {
            $credentialParams[$countryCode] = array('auth' => array());
            foreach ($configPerCountry as $paymentType => $configPerType) {
                if ($paymentType === ConfigurationProvider::CHECKOUT && ($countryCode == "DE" || $countryCode  == "NL")) {
                    $configPerType = array_merge($configPerType, $checkoutCredentials);
                }
                $credentialParams[$countryCode]['auth'][$paymentType] = $configPerType;
            }
            $credentialParams[$countryCode]['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;
        }

        $credentialParams['common']['auth'][ConfigurationProvider::HOSTED_TYPE] = $commonCredentials;
        $credentialParams['common']['auth'][ConfigurationProvider::CHECKOUT] = $checkoutCredentials;

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
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => self::SWP_TEST_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_TEST_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_TEST_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_TEST_PREPARED_URL,
            ConfigurationProvider::CHECKOUT => self::CHECKOUT_TEST_BASE_URL,
            ConfigurationProvider::CHECKOUT_ADMIN => self::CHECKOUT_ADMIN_TEST_BASE_URL
        );
    }

    private static function getProdUrls()
    {
        return array(
            ConfigurationProvider::HOSTED_TYPE => self::SWP_PROD_URL,
            ConfigurationProvider::INVOICE_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::PAYMENTPLAN_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => self::SWP_PROD_WS_URL,
            ConfigurationProvider::HOSTED_ADMIN_TYPE => self::SWP_PROD_HOSTED_ADMIN_URL,
            ConfigurationProvider::ADMIN_TYPE => self::SWP_PROD_ADMIN_URL,
            ConfigurationProvider::PREPARED_URL => self::SWP_PROD_PREPARED_URL,
            ConfigurationProvider::CHECKOUT => self::CHECKOUT_PROD_BASE_URL,
            ConfigurationProvider::CHECKOUT_ADMIN => self::CHECKOUT_ADMIN_PROD_BASE_URL
        );
    }
}
