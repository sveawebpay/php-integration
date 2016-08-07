<?php

namespace Svea\WebPay\WebService\WebServiceResponse\CustomerIdentity;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderIdentity extends CustomerIdentityResponse
{
    public $email;
    public $ipAddress;
    public $countryCode;
    public $houseNumber;

    /**
     * CreateOrderIdentity constructor.
     * @param object $customer
     */
    function __construct($customer)
    {
        $this->email = isset($customer->Email) ? $customer->Email : "";
        $this->ipAddress = isset($customer->IpAddress) ? $customer->IpAddress : "";
        $this->countryCode = isset($customer->CountryCode) ? $customer->CountryCode : "";
        $this->houseNumber = isset($customer->HouseNumber) ? $customer->HouseNumber : "";

        parent::__construct($customer);
    }
}
