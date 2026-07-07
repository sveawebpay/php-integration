<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCustomerIdentity
{
    /**
     * Only include in Nordic countries
     */
    public $NationalIdNumber;
    public $Email;
    public $PhoneNumber;
    public $IpAddress;
    public $FullName;
    public $Street;
    public $CoAddress;
    public $ZipCode;
    public $HouseNumber;
    public $Locality;
    public $CountryCode;
    public $CustomerType;
    public $PublicKey;
    public $IndividualIdentity;
    public $CompanyIdentity;

    /**
     * Dynamically crate an instancevariable depending on Company or Individual
     * @param array $identity
     */
    public function __construct($identity = array())
    {
        if (isset($identity)) {
            foreach ($identity as $key => $value) {
                if ($key == 'IndividualIdentity') {
                    $this->IndividualIdentity = $value;
                } else {
                    $this->CompanyIdentity = $value;
                }
            }
        }
    }
}
