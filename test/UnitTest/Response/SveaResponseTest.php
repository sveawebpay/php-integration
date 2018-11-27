<?php

namespace Svea\WebPay\Test\UnitTest\Response;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Response\SveaResponse;
use Svea\WebPay\Config\ConfigurationProvider;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class SveaResponseTest extends \PHPUnit\Framework\TestCase
{

    public function test_handles_response_which_is_null()
    {
        $config = ConfigurationService::getDefaultConfig();

        $response = new SveaResponse(NULL, "SE", $config);
        $this->assertInternalType('string', $response->getResponse());
        $this->assertEquals('Response is not recognized.', $response->getResponse());
    }

    public function test_handles_response_which_is_xml()
    {
        $config = ConfigurationService::getDefaultConfig();

        $message = "string_that_pretends_to_be_an_encoded_xml_response";

        $response = new SveaResponse($message, "SE", $config);
        $this->assertInstanceOf('Svea\WebPay\HostedService\HostedResponse\HostedPaymentResponse', $response->getResponse());

    }

    public function test_handles_response_is_SimpleXMLElement_object()
    {
        $config = ConfigurationService::getDefaultConfig();

        $message = (object)array("CloseOrderEuResult" => (object)array("Accepted" => "1", "ResultCode" => "0"));

        $this->assertTrue(\is_object($message));

        $response = new SveaResponse($message, "SE", $config);
        $this->assertInstanceOf('Svea\WebPay\WebService\WebServiceResponse\CloseOrderResult', $response->getResponse());
    }

    /**
     * Investigation of github php-integration issue #39.
     *
     * Also, an example of how to parse a HostedPaymentRequest response, i.e. at
     * the Svea\WebPay\WebPay::createOrder()->..->useCardPayment->getPaymentForm() returnurl
     * by passing the response post data through Svea\WebPay\Response\SveaResponse() to get a response
     * object matching the original payment request.
     */
    public function test_successful_test_card_order_has_accepted_non_zero()
    {

        // getSingleCountryConfig fetches a SveaConfigurationProvider object that implements Svea\WebPay\Config\ConfigurationProvider
        // as we don't set any parameters, the object contains only default values, i.e. the merchantid used is 1130
        $config = ConfigurationService::getSingleCountryConfig(
            null, //SE
            null, null, null,
            null, null, null,
            null, null, null,
            null, null,
            false // $prod = false
        );

        // $message, $mac and $merchantid below was taken from server logs for a test card transaction to the merchant 1130
        $message = "PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48cmVzcG9uc2U+PHRyYW5zYWN0aW9uIGlkPSI1ODEzODAiPjxwYXltZW50bWV0aG9kPktPUlRDRVJUPC9wYXltZW50bWV0aG9kPjxtZXJjaGFudGlkPjExMzA8L21lcmNoYW50aWQ+PGN1c3RvbWVycmVmbm8+MzY8L2N1c3RvbWVycmVmbm8+PGFtb3VudD4xODU3ODwvYW1vdW50PjxjdXJyZW5jeT5TRUs8L2N1cnJlbmN5PjxjYXJkdHlwZT5WSVNBPC9jYXJkdHlwZT48bWFza2VkY2FyZG5vPjQ0NDQzM3h4eHh4eDExMDA8L21hc2tlZGNhcmRubz48ZXhwaXJ5bW9udGg+MDE8L2V4cGlyeW1vbnRoPjxleHBpcnl5ZWFyPjE1PC9leHBpcnl5ZWFyPjxhdXRoY29kZT40NTM2MjY8L2F1dGhjb2RlPjxjdXN0b21lcj48Zmlyc3RuYW1lLz48bGFzdG5hbWUvPjxpbml0aWFscy8+PGVtYWlsPnRlc3RAdGltLWludGVybmF0aW9uYWwubmV0PC9lbWFpbD48c3NuPjwvc3NuPjxhZGRyZXNzPktsb2NrYXJnYXRhbiA1QzwvYWRkcmVzcz48YWRkcmVzczIvPjxjaXR5PlbDpHN0ZXLDpXM8L2NpdHk+PGNvdW50cnk+U0U8L2NvdW50cnk+PHppcD43MjM0NDwvemlwPjxwaG9uZT40NjcwNDE2MDA5MDwvcGhvbmU+PHZhdG51bWJlci8+PGhvdXNlbnVtYmVyPjU8L2hvdXNlbnVtYmVyPjxjb21wYW55bmFtZS8+PGZ1bGxuYW1lLz48L2N1c3RvbWVyPjwvdHJhbnNhY3Rpb24+PHN0YXR1c2NvZGU+MDwvc3RhdHVzY29kZT48L3Jlc3BvbnNlPg==";
        $mac = "0411ed66739c251308b70c642fc5f7282f89050421408b74bdd909fb0c13c37c4c2efd6da3593dc388dd28952478aeb1ce5259caf33fd68d364fc4f82914e055";

        $merchantId = $config->getMerchantId(ConfigurationProvider::HOSTED_TYPE, "SE");

        // the $rawresponse is similar to what we get posted back to our return url following a CardPayment post to i.e. the certitrade payment page
        $rawresponse = array();
        $rawresponse['response'] = $message;
        $rawresponse['mac'] = $mac;
        $rawresponse['merchantId'] = $merchantId;

        // $rawresponse is then put into the Svea\WebPay\Response\SveaResponse constructor along with the country and config object
        $sveaResponse = new SveaResponse($rawresponse, "SE", $config);
        // the resulting $response HostedPaymentResponse object contains all relevant information about the payment
        $response = $sveaResponse->getResponse();

        // uncomment the following to see the resulting response
//        //print_r( $response ); // accepted is show as having value of 1
//        var_dump( $response );  // note that var_dump lists accepted as 'int(1)' meaning an int with value 1 (in contrast to 'string(3) "SEK"')
//
//        if( empty($response->accepted) ) { //print_r( "test accepted is empty" ); }
//        if( !empty($response->accepted) ) { //print_r( "test accepted not empty" ); }

        $this->assertInstanceOf('Svea\WebPay\HostedService\HostedResponse\HostedPaymentResponse', $response);
        $this->assertEquals(1, $response->accepted);

    }
}