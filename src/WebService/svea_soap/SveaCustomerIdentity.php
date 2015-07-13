<?php
namespace Svea\WebService\WebServiceSoap;

/**
 *
 */
class SveaCustomerIdentity {

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

    /**
     * Dynamically crate an instancevariable depending on Company or Individual
     * @param type array
     */
    public function __construct($identity = array()) {
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
