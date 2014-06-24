<?php
namespace Svea;
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * SveaConfigurationProvider implements the ConfigurationProvider interface.
 * 
 * This class expects to be initialised with an array listing the various
 * configuration settings, see SveaConfig for details on the array structure.
 *
 * The class is used as Default to get the settings in SveaConfig
 * The class can be used as an example when creating your own
 * class implementing the ConfigurationProvider interface.
 * The class should return the right authorization values
 * and is called by the integration package.
 *
 * @author anne-hal
 */
class SveaConfigurationProvider implements \ConfigurationProvider {

    public $conf;

    /**
     * This class expects to be initialised with an array listing the various
     * configuration settings, see SveaConfig for details on the array structure.
     *
     * @see \Svea\SveaConfig::getTestConfig() \Svea\SveaConfig::getTestConfig()
     *  
     * @param array $environmentConfig
     */
    public function __construct($environmentConfig) {
        $this->conf = (array)$environmentConfig;
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getUsername($type, $country) {
        return $this->getCredentialsProperty('username', $type, $country);
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getPassword($type, $country) {
        return $this->getCredentialsProperty('password', $type, $country);
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getClientNumber($type, $country) {
        return $this->getCredentialsProperty('clientNumber', $type, $country);
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getMerchantId($type, $country) {
        return $this->getCredentialsProperty('merchantId', $type, $country);
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getSecret($type, $country) {
        return $this->getCredentialsProperty('secret', $type, $country);
    }

    /**
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @return string
     * @throws Exception
     */
    public function getEndPoint($type) {
        $uType = strtoupper($type);
        if (array_key_exists($uType,$this->conf['url']) == FALSE) {
            $this->throwInvalidTypeException( $type );
        }
        return $this->conf['url'][$uType];
    }

    /**
     * @param string $property
     * @param string $type one of { \ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    private function getCredentialsProperty($property, $type, $country)
    {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
        if (array_key_exists($uCountry,$this->conf['credentials']) == FALSE) {

            $this->throwInvalidCountryException();
        } elseif (array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE) {
            $this->throwInvalidTypeException();
        }

        return $this->conf['credentials'][$uCountry]['auth'][$uType][$property];
    }

    /**
     * @throws InvalidCountryException
     */
    private function throwInvalidCountryException() {
        throw new InvalidCountryException('Invalid or missing Country code');
    }

    /**
     * @throws InvalidTypeException
     */
    private function throwInvalidTypeException( $invalid_type ) {
        throw new InvalidTypeException(
            sprintf(
                'Invalid service type \"%s\". Accepted values: %s, %s, %s or %s',
                $invalid_type,
                \ConfigurationProvider::INVOICE_TYPE,
                \ConfigurationProvider::PAYMENTPLAN_TYPE,
                \ConfigurationProvider::HOSTED_TYPE,
                \ConfigurationProvider::HOSTED_ADMIN_TYPE
            )
        );
    }
}
