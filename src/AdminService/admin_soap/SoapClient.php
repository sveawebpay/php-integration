<?php
namespace Svea\AdminService\AdminSoap;

class SoapClient {

    private $client;

    /**
     * Constructor, sets up soap server and SoapClient
     * @param string $endpoint
     */
    public function __construct($endpoint) {
        $this->client = $this->setSoapClient( $endpoint );
    }

    /**
     * When used from PHP, the SveaWebPay Administration Service requires some configuration.
     * getSoapClient() takes the endpoint url and returns a SoapClient with a working set
     * of options, bypassing the server wsdl.
     *
     * @param string $endpoint
     * @return SoapClient
     */
    public function setSoapClient( $endpoint ) {
        $client = new \SoapClient(
            null,
            array(
                'location' => $endpoint,
                'uri' => "http://tempuri.org/",
                'use' => SOAP_LITERAL,
                'exceptions'=> 1,
                'connection_timeout' => 60,
                'trace' => 1,
                'soap_version' => SOAP_1_1
            )
        );

        return $client;
    }

    /**
     * doSoapCall takes the $action name of the soap function you wish to call,
     * and the $request data.
     *
     * Use the provided AdminSoap classes to build the request content, as these
     * perform the \SoapVar conversion. Note that doSoapCall performs the final
     * wrapping of the request contents, so you do not need a "request" SoapVar.
     *
     * @param string $action
     * @param \SoapVar $request
     * @return StdClass $response
     */
    public function doSoapCall($action, $request) {

        // wrap the request
        $wrappedRequest = new \SoapVar( $request, SOAP_ENC_OBJECT, "-", "--", "request", "http://tempuri.org/");

        // do soapcall
        $response = $this->client->__soapCall( $action, array( $wrappedRequest ),
            array( "soapaction" => 'http://tempuri.org/IAdminService/'.$action )
        );

        return $response;
    }
}