<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebService/svea_soap/SveaSoapConfig.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class GetAddressesTest extends PHPUnit_Framework_TestCase {

    public function testBuildRequest() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $addressRequest
                ->setCountryCode("SE")
                ->setCompany("SE460509");
        $this->assertEquals("SE", $addressRequest->countryCode);
        $this->assertEquals("SE460509", $addressRequest->companyId);
    }

    public function testPrepareRequestPrivate() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual(194605092222)
                ->prepareRequest();

        $this->assertEquals(79021, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
        $this->assertEquals(FALSE, $request->request->IsCompany);
        $this->assertEquals("SE", $request->request->CountryCode);
        $this->assertEquals(194605092222, $request->request->SecurityNumber);
    }

    public function testPrepareRequestCompany() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setCompany(4608142222)
                ->prepareRequest();

        $this->assertEquals(79021, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
        $this->assertEquals(true, $request->request->IsCompany);
        $this->assertEquals("SE", $request->request->CountryCode);
        $this->assertEquals(4608142222, $request->request->SecurityNumber);
    }
    
    /// validations
    public function test_missing_countryCode_throws_exception() {
        $this->setExpectedException(
          '\Svea\ValidationException',"countryCode is required. Use function setCountryCode()."
        );         
 
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() )
                //->setCountryCode( "SE" )
                ->setCustomerIdentifier("4605092222")
                ->getIndividualAddresses()
                    ->prepareRequest();
    } 
    
    public function test_getIndividualAddresses_with_missing_customerIdentifier_throws_exception() {
        $this->setExpectedException(
          '\Svea\ValidationException',"customerIdentifier is required. Use function setCustomerIdentifer()."
        );         
    
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() )
                ->setCountryCode( "SE" )
                //->setCustomerIdentifier("4605092222")
                ->getIndividualAddresses()
                    ->prepareRequest();
    }
    
    public function test_getCompanyAddresses_with_missing_customerIdentifier_throws_exception() {
        $this->setExpectedException(
          '\Svea\ValidationException',"customerIdentifier is required. Use function setCustomerIdentifer()."
        );         
    
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() )
                ->setCountryCode( "SE" )
                //->setCustomerIdentifier("4605092222")
                ->getCompanyAddresses()
                    ->prepareRequest();
    }    
    
    public function test_missing_Configuration_for_CountryCode_throws_exception() {
        $this->setExpectedException(
          '\Svea\ValidationException',"missing authentication credentials. Check configuration."
        );         
    
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() );

        // clear both payment method credentials for SE
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['username'] = null;        
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['password'] = null;
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['clientNumber'] = null;
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['username'] = null;        
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['password'] = null;
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['clientNumber'] = null;
           
        $request        
            ->setCountryCode( "SE" )
            ->setCustomerIdentifier("4605092222")
            ->getIndividualAddresses()
                ->prepareRequest();
    }
    
    public function test_checkAndSetConfiguredPaymentMethod_finds_invoice_configuration() {     
    
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() );

        // clear both payment method credentials for SE
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['username'] = null;        
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['password'] = null;
        $request->conf->conf['credentials']['SE']['auth']['Invoice']['clientNumber'] = null;
//        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['username'] = null;        
//        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['password'] = null;
//        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['clientNumber'] = null;
           
        $request        
            ->setCountryCode( "SE" )
            ->setCustomerIdentifier("4605092222")
            ->getIndividualAddresses()
                ->prepareRequest();

        $this->assertEquals( ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderType );
    }    

    public function test_checkAndSetConfiguredPaymentMethod_finds_paymentplan_configuration() {     
    
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() );

        // clear both payment method credentials for SE
//        $request->conf->conf['credentials']['SE']['auth']['Invoice']['username'] = null;        
//        $request->conf->conf['credentials']['SE']['auth']['Invoice']['password'] = null;
//        $request->conf->conf['credentials']['SE']['auth']['Invoice']['clientNumber'] = null;
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['username'] = null;        
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['password'] = null;
        $request->conf->conf['credentials']['SE']['auth']['PaymentPlan']['clientNumber'] = null;
           
        $request        
            ->setCountryCode( "SE" )
            ->setCustomerIdentifier("4605092222")
            ->getIndividualAddresses()
                ->prepareRequest();

        $this->assertEquals( ConfigurationProvider::INVOICE_TYPE, $request->orderType );
    }  

}
