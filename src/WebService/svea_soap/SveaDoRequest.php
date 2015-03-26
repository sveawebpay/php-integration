<?php
namespace Svea\WebService\WebServiceSoap;

require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Create SoapObject
 * Do request
 * @return Response Object
 */
class SveaDoRequest {

    private $svea_server;
    private $client;

    /**
     * Constructor, sets up soap server and SoapClient
     * @param ConfigurationProvider $config
     * @param string $orderType -- see ConfigurationProvider:: constants
     */
    public function __construct($config, $ordertype) {        
        $this->svea_server = $config->getEndPoint($ordertype);        
        $this->client = $this->SetSoapClient( $config );        
    }
    
    private function SetSoapClient($config) {
           
        $libraryProperties = \Svea\Helper::getSveaLibraryProperties();
        $libraryName = $libraryProperties['library_name'];
        $libraryVersion =  $libraryProperties['library_version'];

        $integrationProperties = \Svea\Helper::getSveaIntegrationProperties($config);
        $integrationPlatform = $integrationProperties['integration_platform'];
        $integrationCompany = $integrationProperties['integration_company'];
        $integrationVersion = $integrationProperties['integration_version'];        
                        
        $client = new \SoapClient(             
            $this->svea_server, 
            array(
                "trace" => 1,
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
     * Create Invoice or Partpaymentorder
     * @param mixed $order Object containing SveaAuth and SveaCreateOrderInformation
     * @return CreateOrderEuResponse Object
     */
    public function CreateOrderEu($order) {
        $builder = new SveaSoapArrayBuilder();
        return $this->client->CreateOrderEu($builder->object_to_array($order)); //result of SoapClient CreateOrderEu method

    }

//    /**
//     * Use to get Addresses based on NationalIdNumber or orgnr. Only in SE, NO, DK.
//     * @param type $request Object containing SveaAuth, IsCompany, CountryCode, SecurityNumber
//     * @return GetAddressesResponse Object.
//     */
    public function GetAddresses($request) {
        $builder = new SveaSoapArrayBuilder();
        return $this->client->GetAddresses($builder->object_to_array($request));
    }

//    /**
//     * Use to get params om partpayment options
//     * @param type SveaAuth Object
//     * @return CampaignCodeInfo Object
//     */
    public function GetPaymentPlanParamsEu($auth) {
        $builder = new SveaSoapArrayBuilder();
        return $this->client->GetPaymentPlanParamsEu($builder->object_to_array($auth));
    }

//    /**
//     *
//     * @param type $deliverdata Object containing SveaAuth and DeliverOrderInformation
//     * @return DeliverOrderResult Object
//     */
    public function DeliverOrderEu($deliverdata) {
        $builder = new SveaSoapArrayBuilder();
        return $this->client->DeliverOrderEu($builder->object_to_array($deliverdata));
    }

    public function CloseOrderEu($closedata) {
        $builder = new SveaSoapArrayBuilder();
        return $this->client->CloseOrderEu($builder->object_to_array($closedata));
    }
}
