<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith, Kristian Grossman-Madsen
 */
class GetAddressesIntegrationTest extends PHPUnit_Framework_TestCase {

    // private, company
    public function testGetAddressesResult_Private() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("194605092222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    public function testGetAddressesResult_Company() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    public function testGetAddressesResult_PaymentPlan() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypePaymentPlan()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    public function testGetAddressesResult_Invoice() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }
    
    public function test_GetAddressesResult_Invoice_NoSuchEntity() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("4608142222")   // setIndividual w/Company SSN
            ->doRequest();
        $this->assertEquals(0, $request->accepted);
        $this->assertEquals("NoSuchEntity", $request->resultcode);
    }
        
    public function test_GetAddressesResult_Invoice_Errormessage() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("4608142222")   // setIndividual w/Company SSN
            ->doRequest();
        $this->assertEquals(0, $request->accepted);
        $this->assertEquals("NoSuchEntity", $request->resultcode);
        $this->assertEquals("No customer address was found", $request->errormessage);
    }        

    public function test_GetAddresses_CredentialsForPrivate_areCorrect() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual("194605092222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
        //$this->assertEquals('5F445B19E8C87954904FB7531A51AEE57C5E9413', $request->customerIdentity[0]->addressSelector);
        $this->assertEquals('Person', $request->customerIdentity[0]->customerType);
        $this->assertEquals('08 - 111 111 11', $request->customerIdentity[0]->phoneNumber);
        $this->assertEquals('Persson, Tess T', $request->customerIdentity[0]->fullName);
        $this->assertEquals('Tess T', $request->customerIdentity[0]->firstName);
        $this->assertEquals('Persson', $request->customerIdentity[0]->lastName);
        $this->assertEquals('Testgatan 1', $request->customerIdentity[0]->street);
        $this->assertEquals('c/o Eriksson, Erik', $request->customerIdentity[0]->coAddress);
        $this->assertEquals(99999, $request->customerIdentity[0]->zipCode);
        $this->assertEquals('Stan', $request->customerIdentity[0]->locality);
        $this->assertEquals(4605092222, $request->customerIdentity[0]->nationalIdNumber);
    }

    // getAddresses is supported for the following countries and customer types
    // SE/private
    public function test_GetAddresses_Sweden_Private_isAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual("194605092222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // DK/private
    public function test_GetAddresses_Denmark_Private_isAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DK")
                ->setIndividual("2603692503")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // SE/company
    public function test_GetAddresses_Sweden_Company_isAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setCompany("4608142222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // DK/company
    public function test_GetAddresses_Denmark_Company_isAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DK")
                ->setCompany("99999993")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // NO/company
    public function test_GetAddresses_Norway_Company_isAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("NO")
                ->setCompany("923313850")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }

    // NO/private
    public function test_GetAddresses_Norway_Private_isDisabled() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("NO")
                ->setCompany("17054512066")
                ->doRequest();

        //disabled oct-13
        $this->assertEquals(0, $request->accepted);
        $this->assertEquals('Error', $request->resultcode);
    }

    // DE
    public function test_GetAddresses_Germany_Company_isNotImplemented() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DE")
                ->setCompany("19680403")
                ->doRequest();

        $this->assertEquals(0, $request->accepted);
        $this->assertEquals('Error', $request->resultcode);
    }

    // NL
    public function test_GetAddresses_Netherlands_Company_isNotImplemented() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getAddresses($config);
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("NL")
                ->setCompany("19550307")
                ->doRequest();

        $this->assertEquals(0, $request->accepted);
        $this->assertEquals('Error', $request->resultcode);
    }
}
