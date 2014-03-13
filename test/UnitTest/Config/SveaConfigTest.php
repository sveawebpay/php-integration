<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

class SveaConfigTest extends \PHPUnit_Framework_TestCase {

    function testSveaConfigNotFound(){
        $config = SveaConfig::getTestConfig();
        $foo = \WebPay::createOrder($config);

        $this->assertEquals("sverigetest", $config->conf['credentials']['SE']['auth']['INVOICE']['username']);
    }
    
    public function t_estInstancesOfSveaConfig() {

        $obj1 = SveaConfig::getConfig();
        $obj2 = SveaConfig::getConfig();
        $this->assertEquals($obj1->password, $obj2->password);

        $obj1->password = "Hej";
        $this->assertNotEquals($obj1->password, $obj2->password);
    }

    public function testOrderWithSEConfigFromFunction() {
           $request = \WebPay::createOrder(SveaConfig::getTestConfig())
            ->addOrderRow(\TestUtil::createOrderRow())
            ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
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
}
