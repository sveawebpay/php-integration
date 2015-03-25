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
        return "integration_name";
    }
    public function getIntegrationCompany() {
        return "Svea WebPay";
    }
    public function getIntegrationVersion() {
        return 'integration_version';
    }
}
