<?php
// ConfigurationProvider interface is not included in Svea namespace

/**
 * Implementation of class gives possibility to connect the package to a
 * shop administration configuration user interface.
 * The params $type and $country can be used to organize your configuration differently for
 * different countries and paymenttypes.
 *
 * Usage: Create one or more classes that implements the \ConfigurationProvider
 * Interface (eg. one class for testing values, one for production values).
 * The implemented functions should return the authorization values for the
 * configuration in question.
 *
 * The integration package will then call these functions to get the respective
 * values from your database. When starting an WebPay action in your integration
 * file, put an instance of your class as parameter to the constructor.
 *
 * @author anne-hal
 */
interface ConfigurationProvider {

    const HOSTED_TYPE = 'HOSTED';
    const INVOICE_TYPE = 'INVOICE';
    const PAYMENTPLAN_TYPE = 'PAYMENTPLAN';
    const HOSTED_ADMIN_TYPE = 'HOSTED_ADMIN';

    /**
     * @return Username received from Svea
     * @param $type eg. "Invoice" or "PaymentPlan" can be used if needed to match different configuration settings
     * @param $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException in case of unsupported $type
     * @throws InvalidCountryException in case of unsupported $country
     */
    public function getUsername($type, $country);

    /**
     * @return Password received from Svea
     * @param $type eg. "Invoice" or "PaymentPlan" can be used if needed to match different configuration settings
     * @param $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException in case of unsupported $type
     * @throws InvalidCountryException in case of unsupported $country
     */
    public function getPassword($type, $country);

    /**
     *
     * @return ClientNumber received from Svea
     * @param $type eg. "Invoice" or "PaymentPlan" can be used if needed to match different configuration settings
     * @param $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE can be used if needed to match different configuration settings
     * @throws InvalidTypeException in case of unsupported $type
     * @throws InvalidCountryException in case of unsupported $country
     */
    public function getClientNumber($type, $country);

    /**
     * @return MerchantId received from Svea
     * @param $type one of ConfigurationProvider::HOSTED_TYPE can be used if needed to match different configuration settings
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getMerchantId($type, $country);

    /**
     * @return Secret word received from Svea
     * @param $type one of ConfigurationProvider::HOSTED_TYPE can be used if needed to match different configuration settings
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getSecret($type, $country);

    /**
     * Constants for the endpoint url found in the class SveaConfig.php
     * getEndPoint() should return an url corresponding to $type.
     *
     * @param $type one of ConfigurationProvider::HOSTED_TYPE, "Invoice", "PaymentPlan", ::HOSTED_ADMIN_TYPE
     */
    public function getEndPoint($type);
}
