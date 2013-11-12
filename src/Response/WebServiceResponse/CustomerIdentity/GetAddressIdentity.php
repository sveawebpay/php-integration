<?php
namespace Svea;

require_once 'CustomerIdentityResponse.php';

/**
 * @author anne-hal
 */
class GetAddressIdentity extends CustomerIdentityResponse {

    public $addressSelector;    // not guaranteed to be defined

}
