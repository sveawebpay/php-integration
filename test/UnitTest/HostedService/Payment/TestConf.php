<?php
namespace Svea\WebPay\Test\UnitTest\HostedService\Payment;

use Exception;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;

/**
 * @author Jonas Lith
 */
class TestConf implements ConfigurationProvider
{

    public function getClientNumber($type, $country)
    {
        return 79021;
    }

    public function getEndPoint($type)
    {
        $type = strtoupper($type);
        if ($type == "HOSTED") {
            return ConfigurationService::SWP_PROD_URL;;
        } elseif ($type == "INVOICE" || $type == "PAYMENTPLAN") {
            return ConfigurationService::SWP_PROD_WS_URL;
        } elseif ($type == "HOSTED_ADMIN") {
            return ConfigurationService::SWP_PROD_HOSTED_ADMIN_URL;
        } elseif ($type == "ADMIN") {
            return ConfigurationService::SWP_PROD_ADMIN_URL;
        } else {
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN, HOSTED_ADMIN or HOSTED');
        }
    }

    public function getMerchantId($type, $country)
    {
        return 1130;
    }

    public function getPassword($type, $country)
    {
        return "sverigetest";
    }

    public function getSecret($type, $country)
    {
        return "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3";
    }

    public function getUsername($type, $country)
    {
        return "sverigetest";
    }

    public function getIntegrationPlatform()
    {
        return "integration_name";
    }

    public function getIntegrationCompany()
    {
        return "Svea Svea\WebPay\WebPay";
    }

    public function getIntegrationVersion()
    {
        return 'integration_version';
    }

    public function getCheckoutMerchantId($country = NULL)
    {
        return 1130;
    }

    public function getCheckoutSecret($country = NULL)
    {
        return "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3";
    }
}
