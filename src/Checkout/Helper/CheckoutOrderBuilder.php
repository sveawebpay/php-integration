<?php

namespace Svea\WebPay\Checkout\Helper;

use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Checkout\Model\IdentityFlags;
use Svea\WebPay\Checkout\Model\MerchantSettings;
use Svea\WebPay\Checkout\Model\PresetValue;
use Svea\WebPay\Checkout\Service\GetOrderService;
use Svea\WebPay\Checkout\Service\CreateOrderService;
use Svea\WebPay\Checkout\Service\UpdateOrderService;
use Svea\WebPay\Checkout\Service\GetAvailablePartPaymentCampaignsService;

/**
 * CheckoutOrderBuilder contains all necessary information
 * for creating a Checkout Order
 *
 * Class CheckoutOrderBuilder
 * @package Svea\Svea\WebPay\WebPay\Checkout\Helper
 */
class CheckoutOrderBuilder extends OrderBuilder
{
	/**
	 * @var float $id
	 */
	protected $id;

	/**
	 * @var MerchantSettings $merchantSettings
	 */
	protected $merchantSettings;

	/**
	 * @var string $locale
	 */
	protected $locale;

	/**
	 * @var PresetValue []
	 */
	protected $presetValues = [];

	/**
	 * @var string $partnerKey
	 */
	protected $partnerKey;

	/**
	 * @var IdentityFlags []
	 */
	protected $identityFlags = [];

	/**
	 * @var string $merchantData
	 */
	protected $merchantData;

	/**
	 * @var bool $requireElectronicIdAuthentication
	 */
	protected $requireElectronicIdAuthentication;

	/**
	 * CheckoutOrderBuilder constructor.
	 * @param \Svea\WebPay\Config\ConfigurationProvider $config
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->merchantSettings = new MerchantSettings();
	}

	/**
	 * Calls logic that initialize creating a Checkout Order
	 * and returns response from server
	 *
	 * @return array
	 */
	public function createOrder()
	{
		$createService = new CreateOrderService($this);

		return $createService->doRequest();
	}

	/**
	 * Calls logic that initialize getting a Checkout Order
	 * and returns response from server
	 *
	 * @return array
	 */
	public function getOrder()
	{
		$getOrderService = new GetOrderService($this);

		return $getOrderService->doRequest();
	}

	/**
	 * Calls logic that initialize updating a Checkout Order
	 * and returns response from server
	 *
	 * @return array
	 */
	public function updateOrder()
	{
		$updateOrderService = new UpdateOrderService($this);

		return $updateOrderService->doRequest();
	}

	/**
	 * Returns all campaigns that is available on the merchant
	 *
	 * @return array
	 */
	public function getAvailablePartPaymentCampaigns()
	{
		$getAvailablePartPaymentCampaigns = new GetAvailablePartPaymentCampaignsService($this);

		return $getAvailablePartPaymentCampaigns->doRequest();
	}

	/**
	 * Set Merchant Settings for an Checkout Order
	 *
	 * @param $merchantSettings
	 * @return $this
	 */
	public function setMerchantSettings($merchantSettings)
	{
		$this->merchantSettings = $merchantSettings;

		return $this;
	}

	/**
	 * @return MerchantSettings
	 */
	public function getMerchantSettings()
	{
		return $this->merchantSettings;
	}

	/**
	 * @param string $checkoutUri
	 * @return $this
	 */
	public function setCheckoutUri($checkoutUri)
	{
		$this->merchantSettings->setCheckoutUri($checkoutUri);

		return $this;
	}

	/**
	 * @param string $confirmationUri
	 * @return $this
	 */
	public function setConfirmationUri($confirmationUri)
	{
		$this->merchantSettings->setConfirmationUri($confirmationUri);

		return $this;
	}

	/**
	 * @param string $pushUri
	 * @return $this
	 */
	public function setPushUri($pushUri)
	{
		$this->merchantSettings->setPushUri($pushUri);

		return $this;
	}

	/**
	 * @param string $termsUri
	 * @return $this
	 */
	public function setTermsUri($termsUri)
	{
		$this->merchantSettings->setTermsUri($termsUri);

		return $this;
	}

	/**
	 * @param string $validationCallbackUri
	 * @return $this
	 */
	public function setValidationCallbackUri($validationCallbackUri)
	{
		$this->merchantSettings->setValidationCallbackUri($validationCallbackUri);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * @param string $locale
	 * @return $this
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Return country code
	 * If country code is not defined, default value "SE" will be returned
	 *
	 * @return string
	 */
	public function getCountryCode()
	{
		$countryCode = $this->countryCode != null ? $this->countryCode : "SE";

		return $countryCode;
	}

	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @param string $clientOrderNumber
	 * @return CheckoutOrderBuilder
	 */
	public function setClientOrderNumber($clientOrderNumber)
	{
		$this->clientOrderNumber = $clientOrderNumber;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getClientOrderNumber()
	{
		return $this->clientOrderNumber;
	}

	/**
	 * Return a list of Preset Values
	 *
	 * @return PresetValue []
	 */
	public function getPresetValues()
	{
		return $this->presetValues;
	}

	/**
	 * Add Preset Value to the list of Preset Values
	 *
	 * @param PresetValue $presetValues
	 * @return $this
	 */
	public function addPresetValue($presetValues)
	{
		$this->presetValues [] = $presetValues;

		return $this;
	}

	/**
	 * Sets a partnerKey which is provided by Svea.
	 * Optional to use
	 * @param string $partnerKey
	 * @return $this
	 */
	public function setPartnerKey($partnerKey)
	{
		$this->partnerKey = $partnerKey;

		return $this;
	}

	/**
	 * Returns a partnerKey
	 * @return string
	 */
	public function getPartnerKey()
	{
		return $this->partnerKey;
	}

	/**
	 * Return a list of IdentityFlags
	 *
	 * @return IdentityFlags []
	 */
	public function getIdentityFlags()
	{
		return $this->identityFlags;
	}

	/**
	 * Add IdentityFlag to the list of IdentityFlags
	 *
	 * @param string $identityFlag
	 * @return $this
	 */
	public function addIdentityFlag($identityFlag)
	{
		$this->identityFlags [] = $identityFlag;

		return $this;
	}

	public function setMerchantData($merchantData)
	{
		$this->merchantData = $merchantData;
		return $this;
	}

	public function getMerchantData()
	{
		return $this->merchantData;
	}

	/**
	 * Enable electronic id authentication for end-user if set to true
	 *
	 * @param bool $enabled
	 * @return $this
	 */
	public function setRequireElectronicIdAuthentication($enabled)
	{
		$this->requireElectronicIdAuthentication = $enabled;
		return $this;
	}

	public function getRequireElectronicIdAuthentication()
	{
		return $this->requireElectronicIdAuthentication;
	}

}
