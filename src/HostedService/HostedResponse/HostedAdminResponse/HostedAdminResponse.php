<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Handles diverse administrative function responses from the webservice and
 * wrapped legacy services through the webservice.
 *
 * @author anne-hal, Kristian Grossman-Madsen for Svea WebPay
 */
class HostedAdminResponse extends HostedResponse{

    protected $config;

    /**
     * Create an new HostedAdminResponse which handles the webservice response
     *
     * Will set response attribute accepted to 0 if the mac is invalid or the
     * response is malformed.
     *
     * @param SimpleXMLElement $message
     * @param string $countryCode
     * @param SveaConfigurationProvider $config
     */
    function __construct($message,$countryCode,$config) {
        $this->config = $config;

        if (is_object($message)) {

            if (property_exists($message,"mac") && property_exists($message,"message")) {
                $decodedXml = base64_decode($message->message);
                $secret = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE,$countryCode);

                if ($this->validateMac($message->message,$message->mac,$secret)) {
                    $this->formatXml($decodedXml);
                } else {
                    $this->accepted = 0;
                    $this->resultcode = '0';
                    $this->errormessage = "Response failed authorization. MAC not valid.";
                }
            }

        } else {
            $this->accepted = 0;
            $this->resultcode = '0';
            $this->errormessage = "Response is not recognized.";
        }
    }

    /**
     * formatXml() parses the hosted admin response xml into an object, and
     * then sets the response attributes accordingly.
     *
     * @param type $hostedAdminResponseXML
     */
    protected function formatXml($hostedAdminResponseXML) {
        $hostedAdminResponse = new \SimpleXMLElement($hostedAdminResponseXML);

        if ((string)$hostedAdminResponse->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams( (string)$hostedAdminResponse->statuscode );
        }

        // getPaymentUrl/preparepayment request
        if( property_exists($hostedAdminResponse,"preparedpayment")) {
            $url = $this->config->getEndpoint( \Svea\SveaConfigurationProvider::PREPARED_URL);
            $testurl = \Svea\SveaConfig::SWP_TEST_PREPARED_URL;

            $this->id = (string)$hostedAdminResponse->preparedpayment->id;
            $this->created = (string)$hostedAdminResponse->preparedpayment->created;

            $this->url = $url.$this->id;            // in integration package only
            $this->testurl = $testurl.$this->id;    // in integration packge only
        }
    }

}
