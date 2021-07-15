<?php

namespace Svea\WebPay\Config;

/**
 * You may modify this file to enable use of the integration package with your
 * own account credentials. Replace the values in the arrays with your credentials
 * (supplied by your Svea account manager).
 */
return [
	'integrationParams' => [
		'integrationcompany' => "myIntegrationCompany",
		'integrationversion' => "myIntegrationVersion",
		'integrationplatform' => "myIntegrationPlatform"
	],
	'commonCredentials' => [
		'merchantId' => '',
		'secret' => ''
	],
	'checkoutCredentials' => [
		'checkoutMerchantId' => '',
		'checkoutSecret' => ''
	],
	'defaultCountryCode' => 'SE',
	'credentials' => [
		'SE' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::CHECKOUT => [
				'username' => '',
				'password' => ''
			]
		],
		'NO' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::CHECKOUT => [
				'username' => '',
				'password' => ''
			]
		],
		'FI' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::CHECKOUT => [
				'username' => '',
				'password' => ''
			]
		],
		'DK' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			]
		],
		'NL' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			]
		],
		'DE' => [
			ConfigurationProvider::INVOICE_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::PAYMENTPLAN_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			],
			ConfigurationProvider::ACCOUNTCREDIT_TYPE => [
				'username' => '',
				'password' => '',
				'clientNumber' => ''
			]
		]
	]
];