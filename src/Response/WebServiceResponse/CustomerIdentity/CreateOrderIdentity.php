<?php
namespace Svea;

require_once 'CustomerIdentityResponse.php';

/**
 * 
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderIdentity extends CustomerIdentityResponse {

    public $email;
    public $ipAddress;
    public $countryCode;
    public $houseNumber;
}
