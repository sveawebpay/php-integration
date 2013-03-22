<?php
/**
 *
 * @author anne-hal
 */
interface ConfigurationProvider {
    public function getUsername($type, $country);
    public function getPassword($type, $country);
    public function getclientNumber($type, $country);
    public function getMerchantId($type, $country);
    public function getSecret($type, $country);
    public function getEndPoint($type);
}

?>
