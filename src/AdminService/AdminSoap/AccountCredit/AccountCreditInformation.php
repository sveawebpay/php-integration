<?php

namespace Svea\WebPay\AdminService\AdminSoap\AccountCredit;

class AccountCreditInformation
{
    public $clientAccountCreditId;
    public $clientId;

    /**
     * AccountCreditInformation constructor.
     * @param $clientAccountCreditId
     * @param $clientId
     */
    public function __construct($clientAccountCreditId = null, $clientId = null)
    {
        $this->clientAccountCreditId = $clientAccountCreditId;
        $this->clientId = $clientId;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientAccountCreditId()
    {
        return $this->clientAccountCreditId;
    }

    /**
     * @param mixed $clientAccountCreditId
     * @return $this
     */
    public function setClientAccountCreditId($clientAccountCreditId)
    {
        $this->clientAccountCreditId = $clientAccountCreditId;

        return $this;
    }


}