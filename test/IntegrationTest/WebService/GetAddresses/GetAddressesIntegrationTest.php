<?php
// Integration tests should not need to use the namespace

use \Svea\WebService\GetAddresses as GetAddresses;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith, Kristian Grossman-Madsen
 */
class GetAddressesIntegrationTest extends PHPUnit_Framework_TestCase {

    private $config;
    private $addressRequest;
    
    public function SetUp() {
        $this->config = Svea\SveaConfig::getDefaultConfig();
        $this->addressRequest = new GetAddresses($this->config);
    }
    
    // private, company
    public function testGetAddressesResult_Private() {
        $request = $this->addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("194605092222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }
    
    public function testGetAddressesResult_Company() {
        $request = $this->addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    public function testGetAddressesResult_PaymentPlan() {
        $request = $this->addressRequest
            ->setOrderTypePaymentPlan()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    public function testGetAddressesResult_Invoice() {
        $request = $this->addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany("4608142222")
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }
    
    public function test_GetAddressesResult_Invoice_NoSuchEntity() {
        $request = $this->addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("4608142222")   // setIndividual w/Company SSN
            ->doRequest();
        $this->assertEquals(0, $request->accepted);
        $this->assertEquals("NoSuchEntity", $request->resultcode);
    }
        
    public function test_GetAddressesResult_Invoice_Errormessage() {
        $request = $this->addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual("4608142222")   // setIndividual w/Company SSN
            ->doRequest();
        $this->assertEquals(0, $request->accepted);
        $this->assertEquals("NoSuchEntity", $request->resultcode);
        $this->assertEquals("No customer address was found", $request->errormessage);
    }        

    public function test_GetAddresses_CredentialsForPrivate_areCorrect() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual("194605092222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
        $this->assertEquals('5F445B19E8C87954904FB7531A51AEE57C5E9413', $request->customerIdentity[0]->addressSelector);
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

    public function test_GetAddresses_CredentialsForCompany_areCorrect() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setCompany("164608142222")    // 12 digit orgnr should start with 16 or be 10 digits.
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
        $this->assertEquals('5F445B19E8C87954904FB7531A51AEE57C5E9413', $request->customerIdentity[0]->addressSelector);
        $this->assertEquals('Business', $request->customerIdentity[0]->customerType);
        $this->assertEquals('08 - 111 111 11', $request->customerIdentity[0]->phoneNumber);
        $this->assertEquals('Persson, Tess T', $request->customerIdentity[0]->fullName);     
        $this->assertEquals('Tess T', $request->customerIdentity[0]->firstName);
        $this->assertEquals('Persson', $request->customerIdentity[0]->lastName);
        $this->assertEquals('Testgatan 1', $request->customerIdentity[0]->street);
        $this->assertEquals('c/o Eriksson, Erik', $request->customerIdentity[0]->coAddress);
        $this->assertEquals(99999, $request->customerIdentity[0]->zipCode);
        $this->assertEquals('Stan', $request->customerIdentity[0]->locality);
        $this->assertEquals(4608142222, $request->customerIdentity[0]->nationalIdNumber);

        $this->assertEquals('F09E3CC5AFB627CACBE22A7BC371DE0047222F7F', $request->customerIdentity[1]->addressSelector);
        $this->assertEquals('Business', $request->customerIdentity[1]->customerType);
        $this->assertEquals('08 - 111 111 11', $request->customerIdentity[1]->phoneNumber);
        $this->assertEquals('Persson, Tess T', $request->customerIdentity[1]->fullName);
        $this->assertEquals('Tess T', $request->customerIdentity[1]->firstName);
        $this->assertEquals('Persson', $request->customerIdentity[1]->lastName);
        $this->assertEquals('Testgatan 1, 2', $request->customerIdentity[1]->street);
        $this->assertEquals('c/o Eriksson, Erik', $request->customerIdentity[1]->coAddress);
        $this->assertEquals(99999, $request->customerIdentity[1]->zipCode);
        $this->assertEquals('Stan', $request->customerIdentity[1]->locality);
        $this->assertEquals(4608142222, $request->customerIdentity[1]->nationalIdNumber);
    }
    
    // getAddresses is supported for the following countries and customer types
    // SE/private
    public function test_GetAddresses_Sweden_Private_isAccepted() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual("194605092222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // DK/private
    public function test_GetAddresses_Denmark_Private_isAccepted() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DK")
                ->setIndividual("2603692503")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // SE/company
    public function test_GetAddresses_Sweden_Company_isAccepted() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setCompany("4608142222")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // DK/company
    public function test_GetAddresses_Denmark_Company_isAccepted() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DK")
                ->setCompany("99999993")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }
    // NO/company
    public function test_GetAddresses_Norway_Company_isAccepted() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("NO")
                ->setCompany("923313850")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
    }

    // NO/private
    public function test_GetAddresses_Norway_Private_isDisabled() {
        $request = $this->addressRequest
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
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("DE")
                ->setCompany("19680403")
                ->doRequest();

        $this->assertEquals(0, $request->accepted);
        $this->assertEquals('Error', $request->resultcode);
    }

    // NL
    public function test_GetAddresses_Netherlands_Company_isNotImplemented() {
        $request = $this->addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("NL")
                ->setCompany("19550307")
                ->doRequest();

        $this->assertEquals(0, $request->accepted);
        $this->assertEquals('Error', $request->resultcode);
    }
}
