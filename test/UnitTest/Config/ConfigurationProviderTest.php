<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

class ConfigurationProviderTest extends \PHPUnit_Framework_TestCase {
    
    public function testDefaultTestConfig() {
        $config = SveaConfig::getDefaultConfig();
        $this->assertEquals("sverigetest", $config->getUsername("INVOICE","SE"));
        $this->assertEquals("sverigetest", $config->getPassword("PAYMENTPLAN","SE"));
        $this->assertEquals("16997", $config->getClientNumber("PAYMENTPLAN","DE"));
        $this->assertEquals("1130", $config->getMerchantId("HOSTED", "NL"));
        $this->assertEquals("https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL", $config->getEndPoint("INVOICE"));
    }
}
