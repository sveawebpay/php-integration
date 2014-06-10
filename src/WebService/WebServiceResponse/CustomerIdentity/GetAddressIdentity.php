<?php
namespace Svea\WebService;

require_once 'CustomerIdentityResponse.php';

/**
 *  GetAddressIdentity structure
 *
 *  @attrib $addressSelector
 *  
 *  The following are not guaranteed to be set
 *  @property $firstName
 *  @property $lastName
 *
 *  @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressIdentity extends CustomerIdentityResponse {

    public $addressSelector;
    //public $firstName;
    //public $lastName;

    function __construct( $customer ) {
        $this->addressSelector = isset($customer->AddressSelector) ? $customer->AddressSelector : "";        
        parent::__construct( $customer );
    }
}
