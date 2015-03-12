<?php
namespace Svea\WebService;

require_once 'CustomerIdentityResponse.php';

/**
 *  GetAddressIdentity structure
 *
 *  @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressIdentity extends CustomerIdentityResponse {

    /** @var string $addressSelector */
    public $addressSelector;
    /** @var string $firstName  only set in case of a createorder request */
    public $firstName;
    /** @var string $lastName  only set in case of a createorder request */
    public $lastName;

    function __construct( $customer ) {
        $this->addressSelector = isset($customer->AddressSelector) ? $customer->AddressSelector : "";        
        parent::__construct( $customer );
    }
}
