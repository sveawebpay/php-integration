<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * Description of GetAddressesTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class GetAddressesTest extends PHPUnit_Framework_TestCase {

    function testBuildRequest() {
        $addressRequest = WebPay::getAddresses();
        $addressRequest
                //->setTestmode()()
                ->setCountryCode("SE")
                ->setCompany("SE460509");
        $this->assertEquals("SE", $addressRequest->countryCode);
        $this->assertEquals("SE460509", $addressRequest->companyId);
    }

    function testPrepareRequestPrivate() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
                //->setTestmode()()
                ->setOrderTypeInvoice()
                //->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                ->setCountryCode("SE")
                ->setIndividual(194605092222)
                ->prepareRequest();
        //->doRequest();

        $this->assertEquals(79021, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
        $this->assertEquals(FALSE, $request->request->IsCompany);
        $this->assertEquals("SE", $request->request->CountryCode);
        $this->assertEquals(194605092222, $request->request->SecurityNumber);
    }
    
    function testPrepareRequestCompany() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
                //->setTestmode()()
                ->setOrderTypeInvoice()
               // ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                ->setCountryCode("SE")
                ->setCompany(4608142222)
                ->prepareRequest();
        //->doRequest();
        $this->assertEquals(79021, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
        $this->assertEquals(true, $request->request->IsCompany);
        $this->assertEquals("SE", $request->request->CountryCode);
        $this->assertEquals(4608142222, $request->request->SecurityNumber);
    }

}

?>
