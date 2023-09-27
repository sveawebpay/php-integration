<?php

namespace Svea\WebPay\WebService\SveaSoap;

/**
 * Navigation object
 */
class SveaNavigation
{
    /**
     * Confirmation URL
     * 
     * @var string
     */
    public $ConfirmationUrl;

    /**
     * Rejection URL
     *
     * @var string
     */
    public $RejectionUrl;
    
    /**
     * Callback URL
     *
     * @var string
     */
    public function __construct($ConfirmationUrl = '', $RejectionUrl = '')
    {
        $this->ConfirmationUrl = $ConfirmationUrl;
        $this->RejectionUrl = $RejectionUrl;
    }

    /**
     * Set the confirmation url
     *
     * @param string $url
     * @return self
     */
    public function setConfirmationUrl($url)
    {
        $this->ConfirmationUrl = $url;
        return $this;
    }

    /**
     * Set the rejection url
     *
     * @param string $url
     * @return self
     */
    public function setRejectionUrl($url)
    {
        $this->RejectionUrl = $url;
        return $this;    
    }
}