<?php
namespace Svea;

require_once 'CustomerIdentityResponse.php';

/**
 *  GetAddressIdentity structure
 *
 *  @attrib     $addressSelector    // not guaranteed to be defined
 *
 *  @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressIdentity extends CustomerIdentityResponse {

    public $addressSelector;    // not guaranteed to be defined

}
