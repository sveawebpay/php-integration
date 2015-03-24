<?php
namespace Svea;
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class TestConf implements \ConfigurationProvider {
    
    public function getEndPoint($type) {
        return "url";
    }
    
    public function getMerchantId($type, $country) {
        return "merchant";
    }
    
    public function getPassword($type, $country) {
        return "pass";
    }
    
    public function getSecret($type, $country) {
        return "secret";
    }
    
    public function getUsername($type, $country) {
        return "username";
    }
    
    public function getClientNumber($type, $country) {
        return "clientnumber";
    }
    
    public function getIntegrationPlatform() {
        $library_properties = Helper::getLibraryProperties();
        return $library_properties['integration_name']." UnitTest HostedService Payment TestConf.php";
    }
    public function getIntegrationCompany() {
        return "Svea WebPay";
    }
    public function getIntegrationVersion() {
        $library_properties = Helper::getLibraryProperties();
        return $library_properties['integration_version'];
    }
}
