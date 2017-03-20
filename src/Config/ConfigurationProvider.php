<?php

namespace Svea\WebPay\Config;

use Svea\WebPay\HostedService\Helper\InvalidTypeException;
use Svea\WebPay\HostedService\Helper\InvalidCountryException;

/**
 * Implement this interface to enable the integration package methods to access
 * Svea's various services using your service account credentials.
 *
 * The method params $type and $country can be used to organize your configuration
 * for different countries and payment types.
 *
 * Usage: Create one or more classes that implements the \Svea\WebPay\Config\ConfigurationProvider
 * Interface (eg. one class for testing values, one for production values).
 * The implementing class methods should return your account authorization
 * credentials for the configuration (test, production) in question.
 *
 * The integration package will use the configuration class methods to get the
 * respective credentials, which in turn may be fetched from i.e. your shop
 * database by the methods.
 *
 * Svea provides a sample implementation in the SveaConfigurationProvider class.
 *
 * You may customize configuration parameters into config_prod.php and config_test.php files
 * with your Svea credentials.
 *
 * @see \Svea\SveaConfigurationProvider \Svea\WebPay\Config\SveaConfigurationProvider
 *
 * @author anne-hal
 */
interface ConfigurationProvider
{

    const HOSTED_TYPE = 'HOSTED';
    const INVOICE_TYPE = 'Invoice';
    const PAYMENTPLAN_TYPE = 'PaymentPlan';
    const HOSTED_ADMIN_TYPE = 'HOSTED_ADMIN';
    const ADMIN_TYPE = 'ADMIN';
    const PREPARED_URL = 'PREPARED';
    const ACCOUNT_TYPE = 'Account';
    const CARD_TYPE = 'Card';
    const DIRECT_BANK = 'DirectBank';
    const ACCOUNTCREDIT_TYPE = 'AccountCredit';

    /*
     *  Checkout 
     */
    const CHECKOUT = 'CHECKOUT';
    const CHECKOUT_ADMIN = 'CHECKOUT_ADMIN';

    /**
     * fetch username, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return string
     * @param string $type Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getUsername($type, $country);

    /**
     * fetch password, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return string
     * @param string $type Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getPassword($type, $country);

    /**
     * fetch client number, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return ClientNumber
     * @param string $type Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getClientNumber($type, $country);

    /**
     * fetch merchant id, used with card or direct bank payments (i.e. Svea Hosted Web Service API)
     *
     * @return string
     * @param string $type Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * $param string $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getMerchantId($type, $country);

    /**
     * fetch secret word, used with card or direct bank payments (i.e. Svea Hosted Web Service API)
     *
     * @return string
     * @param string $type Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * $param string $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getSecret($type, $country);

    /**
     * Constants for the endpoint url found in the class ConfigurationService.php
     * getEndPoint() should return an url corresponding to $type.
     *
     * @param string $type one of Svea\WebPay\Config\ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_ADMIN_TYPE, ::ADMIN_TYPE
     */
    public function getEndPoint($type);

    /**
     * fetch Checkout Merchant id, used for Checkout order type
     *
     * @return string
     */
    public function getCheckoutMerchantId();

    /**
     * fetch Checkout Secret word, used for Checkout order type
     *
     * @return string
     */
    public function getCheckoutSecret();

    /**
     * Use this to provide information about your integration platform (i.e. Magento, OpenCart et al), that will be sent to Svea with every service
     * request. Should return a string. The information provided is sent as plain text and should not include any confidential information.
     *
     * Uncomment this if you wish to provide this information from your Svea\WebPay\Config\ConfigurationProvider implementation.
     */
    // public function getIntegrationPlatform();

    /**
     * Use this to provide information about the company providing this particular integration (i.e. Svea Ekonomi, for the Svea Opencart module, et al), that
     * will be sent to Svea with every service request. Should return a string. The information provided is sent as plain text and should not include any
     * confidential information.
     *
     * Uncomment this if you wish to provide this information from your Svea\WebPay\Config\ConfigurationProvider implementation.
     */
    // public function getIntegrationCompany();

    /**
     * Use this to provide information about the version of this particular integration integration platform (i.e. 2.0.1 et al), that will be sent to Svea
     * with every service request. Should return a string. The information provided is sent as plain text and should not include any confidential information.
     *
     * Uncomment this if you wish to provide this information from your Svea\WebPay\Config\ConfigurationProvider implementation.
     */
    // public function getIntegrationVersion();

}
