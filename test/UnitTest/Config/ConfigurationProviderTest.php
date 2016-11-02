<?php

namespace Svea\WebPay\Test\UnitTest\Config;

use Svea\WebPay\Config\ConfigurationService;


class ConfigurationProviderTest extends \PHPUnit_Framework_TestCase {
    
    public function testDefaultTestConfig() {
        $config = ConfigurationService::getDefaultConfig();
        $this->assertEquals("sverigetest", $config->getUsername("Invoice","SE"));
        $this->assertEquals("sverigetest", $config->getPassword("PaymentPlan","SE"));
        $this->assertEquals("16997", $config->getClientNumber("PaymentPlan","DE"));
        $this->assertEquals("1130", $config->getMerchantId("HOSTED", "NL"));
        $this->assertEquals("https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL", $config->getEndPoint("Invoice"));
    }
}
