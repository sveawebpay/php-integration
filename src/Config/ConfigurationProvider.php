<?php
/**
 *  Create a class (eg. one for testing values, one for production) that implements the ConfigurationProvider Interface. Let the implemented functions return the authorization values asked for.
 *  The integration package will then call these functions to get the value from your database.
 *  Later when starting an WebPay action in your integration file, put an instance of your class as parameter to the constructor.
 * @author anne-hal
 */
interface ConfigurationProvider {

    const HOSTED_TYPE = 'HOSTED';
    const INVOICE_TYPE = 'INVOICE';
    const PAYMENTPLAN_TYPE = 'PAYMENTPLAN';

     /**
    * get the return value from your database or likewise
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
    */
    public function getUsername($type, $country);
     /**
    * get the return value from your database or likewise
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
    */
    public function getPassword($type, $country);
     /**
    * get the return value from your database or likewise
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
    */
    public function getClientNumber($type, $country);
     /**
    * get the return value from your database or likewise
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
    */
    public function getMerchantId($type, $country); /**
    * get the return value from your database or likewise
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
    */
    public function getSecret($type, $country);
     /**
    * Constants for the endpoint url found in the class SveaConfig.php
    * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
    */
    public function getEndPoint($type);
}