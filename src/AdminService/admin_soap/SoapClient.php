<?php
namespace Svea\AdminService\AdminSoap;

class SoapClient {

    private $client;

    /**
     * Constructor, sets up soap server and SoapClient
     * @param ConfigurationProvider $config
     * @param string $orderType
     */
    public function __construct($config, $orderType ) {
        $this->client = $this->setSoapClient( $config, $orderType );
    }

    /**
     * When used from PHP, the SveaWebPay Administration Service requires some configuration.
     * getSoapClient() takes the config and eturns a SoapClient with a working set
     * of options, bypassing the server wsdl.
     *
     * @param ConfigurationProvider $config
     * @param string $orderType
     * @return SoapClient
     */
    public function setSoapClient( $config, $orderType ) {
        
        $libraryProperties = \Svea\Helper::getSveaLibraryProperties();
        $libraryName = $libraryProperties['library_name'];
        $libraryVersion =  $libraryProperties['library_version'];
        
        $integrationProperties = \Svea\Helper::getSveaIntegrationProperties($config);
        $integrationPlatform = $integrationProperties['integration_platform'];
        $integrationCompany = $integrationProperties['integration_company'];
        $integrationVersion = $integrationProperties['integration_version'];        
              
        $endpoint = $config->getEndPoint( $orderType );    
        
        $client = new \SoapClient(
            null,
            array(
                'location' => $endpoint,
                'uri' => "http://tempuri.org/",
                'use' => SOAP_LITERAL,
                'exceptions'=> 1,
                'connection_timeout' => 60,
                'trace' => 1,
                'soap_version' => SOAP_1_1,
                'stream_context' => stream_context_create(array('http' => array(
                    'header' => 'X-Svea-Library-Name: ' . $libraryName . "\n" . 
                                'X-Svea-Library-Version: ' . $libraryVersion . "\n" .              
                                'X-Svea-Integration-Platform: ' . $integrationPlatform . "\n" .              
                                'X-Svea-Integration-Company: ' . $integrationCompany . "\n" .              
                                'X-Svea-Integration-Version: ' . $integrationVersion               
                )))
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