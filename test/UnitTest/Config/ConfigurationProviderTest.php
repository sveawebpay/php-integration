<?php

namespace Svea\WebPay\Test\UnitTest\Config;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Helper\Helper;


class ConfigurationProviderTest extends \PHPUnit\Framework\TestCase {
    
    public function testDefaultTestConfig() {
        $config = ConfigurationService::getDefaultConfig();
        $this->assertEquals("sverigetest", $config->getUsername("Invoice","SE"));
        $this->assertEquals("sverigetest", $config->getPassword("PaymentPlan","SE"));
        $this->assertEquals("16997", $config->getClientNumber("PaymentPlan","DE"));
        $this->assertEquals("1130", $config->getMerchantId("HOSTED", "NL"));
        $this->assertEquals("58702", $config->getClientNumber("AccountCredit", "SE"));
        $this->assertEquals("https://webpaywsstage.svea.com/SveaWebPay.asmx?WSDL", $config->getEndPoint("Invoice"));
    }
}
