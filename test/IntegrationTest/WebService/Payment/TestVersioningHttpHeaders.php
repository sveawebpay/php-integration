<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen
 */
class TestVersioningHttpHeadersIntegrationTest extends PHPUnit_Framework_TestCase {
    
    public function test_mockedService_createOrder_useInvoicePayment(){

        $mockedServiceCountry = "SE";
        
        $invoiceUsername = "sverigetest";
        $invoicePassword = "sverigetest";
        $invoiceClientNo = "79021";
        
        $paymentplanUsername = $invoiceUsername;
        $paymentplanPassword = $invoicePassword;
        $paymentplanClientNo = $invoiceClientNo;
        $merchantId = "1130";
        $secret = "wrong_secret";
        
        $mockedCountryConfig[$mockedServiceCountry] = array( "auth" =>
            array(
                \ConfigurationProvider::INVOICE_TYPE =>
                    array("username" => $invoiceUsername, "password" => $invoicePassword, "clientNumber" => $invoiceClientNo),
                \ConfigurationProvider::PAYMENTPLAN_TYPE =>
                    array("username" => $paymentplanUsername, "password" => $paymentplanPassword, "clientNumber" => $paymentplanClientNo),
                \ConfigurationProvider::HOSTED_TYPE =>
                    array("merchantId" => $merchantId, "secret" => $secret),
                "MOCKED_TYPE" =>
                    array("username" => $invoiceUsername, "password" => $invoicePassword, "clientNumber" => $invoiceClientNo)

            )
        );

        $mockedServiceUrl = "http://PC362:8088/mockWebServiceEU?WSDL";   // generated in local SoapUI installation

        $testurl = array(
                       \ConfigurationProvider::HOSTED_TYPE      => Svea\SveaConfig::SWP_TEST_URL,
                       \ConfigurationProvider::INVOICE_TYPE     => Svea\SveaConfig::SWP_TEST_WS_URL,
                       \ConfigurationProvider::PAYMENTPLAN_TYPE => Svea\SveaConfig::SWP_TEST_WS_URL,
                       \ConfigurationProvider::HOSTED_ADMIN_TYPE => Svea\SveaConfig::SWP_TEST_HOSTED_ADMIN_URL,
                       \ConfigurationProvider::ADMIN_TYPE  => Svea\SveaConfig::SWP_TEST_ADMIN_URL,
                       "MOCKED_TYPE" => $mockedServiceUrl
        );
        
        $integrationproperties = array(
                        'integrationcompany' => "myintegrationcompany",
                        'integrationversion' => "myintegrationversion",
                        'integrationplatform' => "myintegrationplatform"
                    )
        ;
        
        $mockedServiceConfig = new Svea\SveaConfigurationProvider( 
            array("url" => $testurl, "credentials" => $mockedCountryConfig, "integrationproperties" => $integrationproperties) 
        );        
        
        $request = WebPay::createOrder($mockedServiceConfig)
                    ->addOrderRow(
                            WebPayItem::orderRow()
                                ->setAmountIncVat(123.9876)
                                ->setVatPercent(24)
                                ->setQuantity(1)
                            )
                    ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                    ->setCountryCode($mockedServiceCountry)
                    ->setOrderDate("2012-12-12")                
        ;        
        $soapRequest = $request->useInvoicePayment()->prepareRequest();
        print_r( $soapRequest );

        $invoicePayment = $request->useInvoicePayment();                
        $invoicePayment->orderType = "MOCKED_TYPE";
//        $soapRequest = $invoicePayment->prepareRequest();
//        print_r( $soapRequest );                
        $response = $invoicePayment->doRequest();
//        print_r( $response );

        $this->assertEquals(1, $response->accepted);
    }
}