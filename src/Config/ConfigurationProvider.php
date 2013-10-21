<?php
// ConfigurationProvider interface is not included in Svea namespace

/**
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
     * get the return value from your database or likewise
     * @param $type eg. INVOICE or PAYMENTPLAN
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getUsername($type, $country);

    /**
     * get the return value from your database or likewise
     * @param $type eg. INVOICE or PAYMENTPLAN
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getPassword($type, $country);

    /**
     * getClientNumber() should return the client number corresponding to $type
     * and $country. Get it from your shop configuration database or likewise.
     *
     * In case of a request for an unsupported payment, i.e. $type is set to
     * ConfigurationProvider::INVOICE_TYPE and we does not have invoice payments
     * configured w/Svea, getEndPoint() should throw an InvalidArgumentException
     *
     * @param $type eg. "INVOICE" or "PAYMENTPLAN"
     * @param $country iso3166 alpha-2 CountryCode, eg. SE, NO, DK, FI, NL, DE
     * @throws InvalidTypeException in case of unsupported $type
     * @throws InvalidCountryException in case of unsupported $country
     */
    public function getClientNumber($type, $country);

    /**
     * get the return value from your database or likewise
     * @param $type one of ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getMerchantId($type, $country);

    /**
     * get the return value from your database or likewise
     * @param $type one of ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE
     * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
     */
    public function getSecret($type, $country);

    /**
     * Constants for the endpoint url found in the class SveaConfig.php
     * getEndPoint() should return an url corresponding to $type.
     *
     * @param $type one of ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_ADMIN_TYPE
     */
    public function getEndPoint($type);
}
