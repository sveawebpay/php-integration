<?php

namespace Svea\WebPay\Test\UnitTest\Config;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;


class SveaConfigTest extends \PHPUnit_Framework_TestCase {

    function testSveaConfigNotFound(){
        $config = ConfigurationService::getTestConfig();
        $foo = WebPay::createOrder($config);

        $this->assertEquals("sverigetest", $config->conf['credentials']['SE']['auth']['Invoice']['username']);
    }

    public function testOrderWithSEConfigFromFunction() {
           $request = WebPay::createOrder(ConfigurationService::getTestConfig())
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()// returnerar InvoiceOrder object
                        ->prepareRequest();

        $this->assertEquals("sverigetest", $request->request->Auth->Username);
        $this->assertEquals("sverigetest", $request->request->Auth->Password);
        $this->assertEquals(79021, $request->request->Auth->ClientNumber);
    }
    
    public function test_getSveaSingleCountryConfig_defaults() {
        $secret = "8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3";
      
        $config = ConfigurationService::getSingleCountryConfig(
                null, // SE
                null, null, null, //invoice 79021
                null, null, null, //partpayment 59999
                null, null, null, //accountCredit 58702
                null, null, // merchantid 1130
                null // test
        );        

        $this->assertInstanceOf('Svea\WebPay\Config\ConfigurationProvider', $config );

        $this->assertEquals("sverigetest", $config->getUsername(ConfigurationProvider::INVOICE_TYPE, "SE") );
        $this->assertEquals("sverigetest", $config->getPassword(ConfigurationProvider::INVOICE_TYPE, "SE") );
        $this->assertEquals("79021", $config->getClientNumber(ConfigurationProvider::INVOICE_TYPE, "SE"));
        $this->assertEquals(ConfigurationService::SWP_TEST_WS_URL, $config->getEndPoint(ConfigurationProvider::INVOICE_TYPE));

        $this->assertEquals("sverigetest", $config->getUsername(ConfigurationProvider::PAYMENTPLAN_TYPE, "SE") );
        $this->assertEquals("sverigetest", $config->getPassword(ConfigurationProvider::PAYMENTPLAN_TYPE, "SE") );
        $this->assertEquals("59999", $config->getClientNumber(ConfigurationProvider::PAYMENTPLAN_TYPE, "SE"));
        $this->assertEquals(ConfigurationService::SWP_TEST_WS_URL, $config->getEndPoint(ConfigurationProvider::PAYMENTPLAN_TYPE));

        $this->assertEquals("sverigetest", $config->getUsername(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "SE") );
        $this->assertEquals("sverigetest", $config->getPassword(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "SE") );
        $this->assertEquals("58702", $config->getClientNumber(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "SE"));
        $this->assertEquals(ConfigurationService::SWP_TEST_WS_URL, $config->getEndPoint(ConfigurationProvider::ACCOUNTCREDIT_TYPE));

        $this->assertEquals("1130", $config->getMerchantId(ConfigurationProvider::HOSTED_TYPE, "SE"));
        $this->assertEquals($secret, $config->getSecret(ConfigurationProvider::HOSTED_TYPE, "SE"));
        $this->assertEquals(ConfigurationService::SWP_TEST_URL, $config->getEndPoint(ConfigurationProvider::HOSTED_TYPE));
    }
    
    public function test_getSveaSingleCountryConfig_respects_passed_parameters() {
 
        $config = ConfigurationService::getSingleCountryConfig(
                "NO",
                "norgetest2", "norgetest2", "33308",
                "norgetest2", "norgetest2", "32503",
                "sverigetest", "sverigetest", "58702",
                "1701", "foo",
                true // $prod = true
        );        
        
        $this->assertInstanceOf('Svea\WebPay\Config\ConfigurationProvider', $config );
        $this->assertEquals("norgetest2", $config->getUsername(ConfigurationProvider::INVOICE_TYPE, "NO") );
        $this->assertEquals("norgetest2", $config->getPassword(ConfigurationProvider::INVOICE_TYPE, "NO") );
        $this->assertEquals("33308", $config->getClientNumber(ConfigurationProvider::INVOICE_TYPE, "NO"));
        $this->assertEquals(ConfigurationService::SWP_PROD_WS_URL, $config->getEndPoint(ConfigurationProvider::INVOICE_TYPE));
        $this->assertEquals("norgetest2", $config->getUsername(ConfigurationProvider::PAYMENTPLAN_TYPE, "NO") );
        $this->assertEquals("norgetest2", $config->getPassword(ConfigurationProvider::PAYMENTPLAN_TYPE, "NO") );
        $this->assertEquals("32503", $config->getClientNumber(ConfigurationProvider::PAYMENTPLAN_TYPE, "NO"));
        $this->assertEquals(ConfigurationService::SWP_PROD_WS_URL, $config->getEndPoint(ConfigurationProvider::PAYMENTPLAN_TYPE));
        $this->assertEquals("sverigetest", $config->getUsername(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "NO") );
        $this->assertEquals("sverigetest", $config->getPassword(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "NO") );
        $this->assertEquals("58702", $config->getClientNumber(ConfigurationProvider::ACCOUNTCREDIT_TYPE, "NO"));
        $this->assertEquals(ConfigurationService::SWP_PROD_WS_URL, $config->getEndPoint(ConfigurationProvider::ACCOUNTCREDIT_TYPE));
        $this->assertEquals("1701", $config->getMerchantId(ConfigurationProvider::HOSTED_TYPE, "NO") );
        $this->assertEquals("foo", $config->getSecret(ConfigurationProvider::HOSTED_TYPE, "NO"));
        $this->assertEquals(ConfigurationService::SWP_PROD_URL, $config->getEndPoint(ConfigurationProvider::HOSTED_TYPE));
    }
    
    /**
     * @expectedException Svea\WebPay\HostedService\Helper\InvalidCountryException
     */
    public function test_getSveaSingleCountryConfig_throws_InvalidCountryException_for_invalid_country() {
        $config = ConfigurationService::getSingleCountryConfig(
                null, // SE
                null, null, null, //invoice 79021
                null, null, null, //partpayment 59999
                null, null, null, //accountcredit 58702
                null, null, // merchantid 1130
                null // test
        );        

        $config->getUsername(ConfigurationProvider::INVOICE_TYPE, "NO");
    }

}
