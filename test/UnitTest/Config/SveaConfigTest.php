<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

class SveaConfigTest extends PHPUnit_Framework_TestCase {
    
    public function t_estInstancesOfSveaConfig() {
        $obj1 = SveaConfig::getConfig();
        $obj2 = SveaConfig::getConfig();
        $this->assertEquals($obj1->password, $obj2->password);

        $obj1->password = "Hej";
        $this->assertNotEquals($obj1->password, $obj2->password);
    }

    public function t_estSetTestmode() {
        $conf = SveaConfig::setConfig()
                ->setMerchantId()
                ->setSecretProd()
                ->setSecretTest()
                ->setPassword()
                ->setUsername()
                ->setClientNumberInvoice()
                ->setClientNumberPaymentPlan()
                ->setAlternativeUrl(); //overwrite all urls

        $request = WebPay::createOrder($conf)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()// returnerar InvoiceOrder object
                       // ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                        ->prepareRequest();
    }
    
    public function testOrderWithSEConfigFromFunction() {
           $request = WebPay::createOrder(SveaConfig::getTestConfig())
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
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

?>
