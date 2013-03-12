<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * Description of SveaConfigTest
 */
class SveaConfigTest extends PHPUnit_Framework_TestCase {
    
    function testInstancesOfSveaConfig(){
        $obj1 = SveaConfig::getConfig();
        $obj2 = SveaConfig::getConfig();
        $this->assertEquals($obj1->password, $obj2->password);
        
        $obj1->password = "Hej";
        $this->assertNotEquals($obj1->password, $obj2->password);
    }
    
    function t_estSetTestmode(){
        $conf = SveaConfig::setConfig()
                ->setMerchantId()
                ->setSecretProd()
                ->setSecretTest()
                ->setPassword()
                ->setUsername()
                ->setClientNumberInvoice()
                ->setClientNumberPaymentPlan()
                ->setTestMode()
                ->setAlternativeUrl(); //overwrite all urls
        
        $request = WebPay::createOrder($conf)
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()// returnerar InvoiceOrder object 
                        ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                        ->prepareRequest();
    }
}

?>
