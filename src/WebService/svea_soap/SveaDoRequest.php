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
     * @param string $serverUrl
     */
    public function __construct($serverUrl) {
        $this->svea_server = $serverUrl;
        $this->client = $this->SetSoapClient();
    }

    private function SetSoapClient() {
        // travis tests time out on php >5.4
        if( version_compare( PHP_VERSION, '5.4.0') >= 0 )
        {
            ini_set('default_socket_timeout', 300);        
            return new \SoapClient($this->svea_server, array('keep_alive' => true, 'connection_timeout' => 300, 'trace' => 1));
        }
        // but work fine on php 5.3
        else {
            return new \SoapClient($this->svea_server, array('trace' => 1));
    }   
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
