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
}
