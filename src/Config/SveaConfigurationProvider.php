<?php

namespace Svea\WebPay\Config;

use \Exception;
use Svea\WebPay\HostedService\Helper\InvalidTypeException;
use Svea\WebPay\HostedService\Helper\InvalidCountryException;

/**
 * SveaConfigurationProvider implements the Svea\WebPay\Config\ConfigurationProvider interface.
 *
 * This class expects to be initialised with an array listing the various
 * configuration settings, see config_prod.php or config_test.php for details on the array structure.e.
 *
 * The class is used as Default to get the settings from config files
 * The class can be used as an example when creating your own
 * class implementing the Svea\WebPay\Config\ConfigurationProvider interface.
 * The class should return the right authorization values
 * and is called by the integration package.
 *
 * @author anne-hal
 */
class SveaConfigurationProvider implements ConfigurationProvider
{
    public $conf;

    /**
     * This class expects to be initialised with an array listing the various
     * configuration settings, see config_prod.php or config_test.php for details on the array structure.
     *
     * @param array $environmentConfig
     */
    public function __construct($environmentConfig)
    {
        $this->conf = (array)$environmentConfig;
    }

    /**
     * @param string $type one of { \Svea\WebPay\Config\ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getUsername($type, $country)
    {
        return $this->getCredentialsProperty('username', $type, $country);
    }

    /**
     * @param string $property
     * @param ConfigurationProvider ::HOSTED_TYPE | ConfigurationProvider::INVOICE_TYPE
     *                                            | ConfigurationProvider::PAYMENTPLAN_TYPE
     *                                            | ConfigurationProvider::ACCOUNTCREDIT_TYPE $type
     * @param string $country
     * @return string
     * @throws Exception
     */
    private function getCredentialsProperty($property, $type, $country)
    {
        if ($property === 'merchantId' || $property === 'secret') {
            if (array_key_exists($property, $this->conf['credentials']['common']['auth'][$type]) === false) {
                $this->throwInvalidCredentialProperty();
            }

            return $this->conf['credentials']['common']['auth'][$type][$property];
        } else {
            return $this->getCredentialsPropertyByCountry($property, $type, $country);
        }
    }

    private function getCredentialsPropertyByCountry($property, $type, $country)
    {
        $uCountry = strtoupper($country);

        if (array_key_exists($uCountry, $this->conf['credentials']) == FALSE) {
            $this->throwInvalidCountryException();
        } elseif (array_key_exists($type, $this->conf['credentials'][$uCountry]['auth']) == FALSE) {
            $this->throwInvalidTypeException($type);
        }

        return $this->conf['credentials'][$uCountry]['auth'][$type][$property];
    }

    /**
     * @throws \Svea\WebPay\HostedService\Helper\InvalidCountryException
     */
    private function throwInvalidCountryException()
    {
        throw new InvalidCountryException('Invalid or missing Country code');
    }

    /**
     * @throws Exception
     */
    private function throwInvalidCredentialProperty()
    {
        throw new Exception('Invalid or missing Credential property');
    }

    /**
     * @param $invalid_type
     * @throws InvalidTypeException
     */
    private function throwInvalidTypeException($invalid_type)
    {
        throw new InvalidTypeException(sprintf('Invalid Svea\WebPay\Config\ConfigurationProvider::XXX_TYPE \"%s\".', $invalid_type));
    }

    /**
     * @param string $type one of { \Svea\WebPay\Config\ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getPassword($type, $country)
    {
        return $this->getCredentialsProperty('password', $type, $country);
    }

    /**
     * @param string $type one of { \Svea\WebPay\Config\ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getClientNumber($type, $country)
    {
        return $this->getCredentialsProperty('clientNumber', $type, $country);
    }

    /**
     * @param string $type one of { \Svea\WebPay\Config\ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getMerchantId($type, $country)
    {
        return $this->getCredentialsProperty('merchantId', $type, $country);
    }

    /**
     * @param string $type one of { ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE }
     * @param string $country
     * @return string
     * @throws Exception
     */
    public function getSecret($type, $country)
    {
        return $this->getCredentialsProperty('secret', $type, $country);
    }

    /**
     * @param string $type one of { ConfigurationProvider::HOSTED_TYPE, ::INVOICE_TYPE, ::PAYMENTPLAN_TYPE,, ::ACCOUNTCREDIT_TYPE ::HOSTED_ADMIN_TYPE, ::ADMIN_TYPE}
     * @return string
     * @throws Exception
     */
    public function getEndPoint($type)
    {
        if (array_key_exists($type, $this->conf['url']) == FALSE) {
            $this->throwInvalidTypeException($type);
        }

        return $this->conf['url'][$type];
    }

    public function getIntegrationPlatform()
    {
        try {
            $integrationplatform = $this->getIntegrationProperty('integrationplatform');
        } catch (InvalidTypeException $e) {
            $integrationplatform = "Please provide your integration platform here.";
        }

        return $integrationplatform;
    }

    private function getIntegrationProperty($property)
    {
        if (array_key_exists('integrationproperties', $this->conf) == FALSE) {
            throw new InvalidTypeException("integration properties not set");
        }

        return $this->conf['integrationproperties'][$property];
    }

    public function getIntegrationVersion()
    {
        try {
            $integrationversion = $this->getIntegrationProperty('integrationversion');
        } catch (InvalidTypeException $e) {
            $integrationversion = "Please provide your integration version here.";
        }

        return $integrationversion;
    }

    public function getIntegrationCompany()
    {
        try {
            $integrationcompany = $this->getIntegrationProperty('integrationcompany');
        } catch (InvalidTypeException $e) {
            $integrationcompany = "Please provide your integration company here.";
        }

        return $integrationcompany;
    }

    /**
     * fetch Checkout Merchant id, used for Checkout order type
     *
     * @return string
     */
    public function getCheckoutMerchantId($country = NULL)
    {
        if($country != NULL)
        {
            return $this->getCredentialsProperty('checkoutMerchantId', ConfigurationProvider::CHECKOUT, $country);
        }
        else
        {
            return $this->getCredentialsProperty('checkoutMerchantId', ConfigurationProvider::CHECKOUT, 'SE');
        }
    }

    /**
     * fetch Checkout Secret word, used for Checkout order type
     *
     * @return string
     */
    public function getCheckoutSecret($country = NULL)
    {
        if($country != NULL)
        {
            return $this->getCredentialsProperty('checkoutSecret', ConfigurationProvider::CHECKOUT, $country);
        }
        else
        {
            return $this->getCredentialsProperty('checkoutSecret', ConfigurationProvider::CHECKOUT, 'SE');
        }
    }
}
