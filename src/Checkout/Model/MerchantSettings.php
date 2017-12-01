<?php

namespace Svea\WebPay\Checkout\Model;

/**
 * This class hold information about urls that
 * are necessary for creating an checkout order
 * 
 * Class MerchantSettings
 * @package Svea\Svea\WebPay\WebPay\Checkout\Model
 */
class MerchantSettings
{
    /**
     * @var string $termsUri
     */
    private $termsUri;

    /**
     * @var string $checkoutUri
     */
    private $checkoutUri;

    /**
     * @var string $confirmationUri
     */
    private $confirmationUri;

    /**
     * @var string $pushUri
     */
    private $pushUri;

    /**
     * @var string $validationCallbackUri
     */
    private $validationCallbackUri;

    /**
     * MerchantSettings constructor.
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getTermsUri()
    {
        return $this->termsUri;
    }

    /**
     * @param string $termsUri
     * @return MerchantSettings
     */
    public function setTermsUri($termsUri)
    {
        $this->termsUri = $termsUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutUri()
    {
        return $this->checkoutUri;
    }

    /**
     * @param string $checkoutUri
     * @return MerchantSettings
     */
    public function setCheckoutUri($checkoutUri)
    {
        $this->checkoutUri = $checkoutUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationUri()
    {
        return $this->confirmationUri;
    }

    /**
     * @param string $confirmationUri
     * @return MerchantSettings
     */
    public function setConfirmationUri($confirmationUri)
    {
        $this->confirmationUri = $confirmationUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getPushUri()
    {
        return $this->pushUri;
    }

    /**
     * @param string $pushUri
     * @return MerchantSettings
     */
    public function setPushUri($pushUri)
    {
        $this->pushUri = $pushUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidationCallbackUri()
    {
         return $this->validationCallbackUri;
    }

     /**
      * @param string $validationCallbackUri
      * @return MerchantSettings
      */
    public function setValidationCallbackUri($validationCallbackUri)
    {
        $this->validationCallbackUri = $validationCallbackUri;
        return $this;
    }

    /**
     * Return structured merchant urls
     * @return array
     */
    public function getMerchantSettings()
    {
        return array(
            'termsUri' => $this->getTermsUri(),
            'checkoutUri' => $this->getCheckoutUri(),
            'confirmationUri' => $this->getConfirmationUri(),
            'checkoutValidationCallBackUri' => $this->getValidationCallbackUri(),
            'pushUri' => $this->getPushUri()
        );
    }
}
