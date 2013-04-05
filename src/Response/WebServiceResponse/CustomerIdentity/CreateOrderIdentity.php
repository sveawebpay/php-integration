<?php
require_once 'CustomerIdentityResponse.php';
/**
 * Description of CustomerIdentityPaymentResponse
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderIdentity extends CustomerIdentityResponse{

    public $email;
    public $ipAddress;
    public $countryCode;
    public $houseNumber;


}