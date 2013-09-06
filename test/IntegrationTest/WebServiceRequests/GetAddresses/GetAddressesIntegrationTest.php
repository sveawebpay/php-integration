<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class GetAddressesIntegrationTest extends PHPUnit_Framework_TestCase {
    
    public function testGetAddressesResultForPrivate() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual(194605092222)
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }
     
    public function testGetAddressesResultForCompany() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany(4608142222)
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }
    
    public function testResultGetAddresses() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual(194605092222)
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
}
