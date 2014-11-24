<?php
// ConfigurationProvider interface is not included in Svea namespace

/**
 * Implement this interface to enable the integration package methods to access
 * Svea's various services using your service account credentials.
 *
 * The method params $type and $country can be used to organize your configuration
 * for different countries and payment types.
 *
 * Usage: Create one or more classes that implements the \ConfigurationProvider
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
 * Instead of writing your own class, you may copy the provided SveaConfig.php
 * file, edit the prodConfig and testConfig arrays, and instantiate your config
 * object from the modified class to use the package with your Svea credentials.
 *
 * @see \Svea\SveaConfigurationProvider \Svea\SveaConfigurationProvider
 * @see \Svea\SveaConfig \Svea\SveaConfig
 *
 * @author anne-hal
 */
interface ConfigurationProvider {

    const HOSTED_TYPE = 'HOSTED';
    const INVOICE_TYPE = 'Invoice';
    const PAYMENTPLAN_TYPE = 'PaymentPlan';
    const HOSTED_ADMIN_TYPE = 'HOSTED_ADMIN';
    const ADMIN_TYPE = 'ADMIN';

    /**
     * fetch username, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return string
     * @param string $type  ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country  iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getUsername($type, $country);

    /**
     * fetch password, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return string
     * @param string $type  ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country  iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getPassword($type, $country);

    /**
     * fetch client number, used with invoice or payment plan (i.e. Svea WebService Europe API)
     *
     * @return ClientNumber
     * @param string $type  ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * @param string $country  iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException  in case of unsupported $type
     * @throws InvalidCountryException  in case of unsupported $country
     */
    public function getClientNumber($type, $country);

    /**
     * fetch merchant id, used with card or direct bank payments (i.e. Svea Hosted Web Service API)
     *
     * @return string
     * @param string $type  ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * $param string $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getMerchantId($type, $country);

    /**
     * fetch secret word, used with card or direct bank payments (i.e. Svea Hosted Web Service API)
     *
     * @return string
     * @param string $type  ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE can be used if needed to match different configuration settings
     * $param string $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getSecret($type, $country);

    /**
     * Constants for the endpoint url found in the class SveaConfig.php
     * getEndPoint() should return an url corresponding to $type.
     *
     * @param string $type one of ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_ADMIN_TYPE, ::ADMIN_TYPE
     */
    public function getEndPoint($type);
}
