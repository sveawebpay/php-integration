<?php

namespace Svea\WebPay\Config;

/**
 * This is Svea Test credentials, and you can use this parameters during developing and testing
 * For production parameters you have to change config_prod.php file
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
    'checkoutCredentials' => array(
        'checkoutMerchantId' => '100002',
        'checkoutSecret' => '3862e010913d7c44f104ddb4b2881f810b50d5385244571c3327802e241140cc692522c04aa21c942793c8a69a8e55ca7b6131d9ac2a2ae2f4f7c52634fe30d2',
    ),
    'defaultCountryCode' => 'SE',
    'credentials' => array(
        'SE' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 79021
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 59999
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => 'sverigetest',
                'password' => 'sverigetest',
                'clientNumber' => 58702
            ),
            ConfigurationProvider::CHECKOUT => array(
                'checkoutMerchantId' => '124842', // Swedish test merchant
                'checkoutSecret' => '1NDxpT2WQ4PW6Ud95rLWKD98xVr45Q8O9Vd52nomC7U9B18jp7lHCu7nsiTJO1NWXjSx26vE41jJ4rul7FUP1cGKXm4wakxt3iF7k63ayleb1xX9Di2wW46t9felsSPW'
            )
        ),
        'NO' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'norgetest2',
                'password' => 'norgetest2',
                'clientNumber' => 33308
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'norgetest2',
                'password' => 'norgetest2',
                'clientNumber' => 32503
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::CHECKOUT => array(
                'checkoutMerchantId' => '124941', // Norwegian test merchant
                'checkoutSecret' => 'XDyrnJnhbvmOch6brKPbF6mVx4NG7Wqzzhm92tsrx3H2IB3m82QxqwM4EUz5Cq9X8kEPpfZxzayB4pfkVEAC2uemgEikIUTf3v1pHxAuRlGuycWt6XyKkjBm9oQxR6pG'
            )
        ),
        'FI' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'finlandtest2',
                'password' => 'finlandtest2',
                'clientNumber' => 26136
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'finlandtest2',
                'password' => 'finlandtest2',
                'clientNumber' => 27136
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            ),
            ConfigurationProvider::CHECKOUT => array(
                'checkoutMerchantId' => '', // No finnish test merchant yet
                'checkoutSecret' => ''
            )
        ),
        'DK' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'danmarktest2',
                'password' => 'danmarktest2',
                'clientNumber' => 62008
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'danmarktest2',
                'password' => 'danmarktest2',
                'clientNumber' => 64008
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'NL' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'hollandtest',
                'password' => 'hollandtest',
                'clientNumber' => 85997
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'hollandtest',
                'password' => 'hollandtest',
                'clientNumber' => 86997
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        ),
        'DE' => array(
            ConfigurationProvider::INVOICE_TYPE => array(
                'username' => 'germanytest',
                'password' => 'germanytest',
                'clientNumber' => 14997
            ),
            ConfigurationProvider::PAYMENTPLAN_TYPE => array(
                'username' => 'germanytest',
                'password' => 'germanytest',
                'clientNumber' => 16997
            ),
            ConfigurationProvider::ACCOUNTCREDIT_TYPE => array(
                'username' => '',
                'password' => '',
                'clientNumber' => ''
            )
        )
    )
);
