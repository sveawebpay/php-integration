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
        'merchantId' => '',
        'secret' => ''
    ),
    'checkoutCredentials' => array(
        'checkoutMerchantId' => '',
        'checkoutSecret' => ''
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::CHECKOUT => array(
                'username' => '',
                'password' => ''
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::CHECKOUT => array(
                'username' => '',
                'password' => ''
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::CHECKOUT => array(
                'username' => '',
                'password' => ''
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
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
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        )
    )
);