<?php

namespace Svea\WebPay\Config;

/**
 * You may modify this file to enable use of the integration package with your
 * own account credentials. Replace the values in the arrays with your credentials
 * (supplied by your Svea account manager).
 */
return array(
    'integrationParams' => array(
        'integrationcompany' => "myIntegrationCompany",
        'integrationversion' => "myIntegrationVersion",
        'integrationplatform' => "myIntegrationPlatform"
    ),
    'commonCredentials' => array(
        'merchantId' => '1130',
        'secret' => '8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3'
    ),
    'defaultCountryCode' => 'SE',
    'credentials' => array(
        'SE' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'NO' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'FI' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'DK' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'NL' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'DE' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        )
    )
);