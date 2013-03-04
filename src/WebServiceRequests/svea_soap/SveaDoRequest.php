<?php

require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * 
 * Create SoapObject
 * Do request
 * @return Response Object
 */
class SveaDoRequest {

    private $svea_server;
    public $client;

    public function __construct($url) {
        $this->svea_server = $url;
        $this->SetSoapClient();
    }

    private function SetSoapClient() {
        $this->client = new SoapClient($this->svea_server, array('trace' => 1));
    }

    /**
     * Create Invoice or Partpaymentorder
     * @param type $order Object containing SveaAuth and SveaCreateOrderInformation
     * @return CreateOrderEuResponse Object
     */
    public function CreateOrderEu($order) {
        $builder = new SveaSoapArrayBuilder();
        
        try {
            $return = $this->client->CreateOrderEu($builder->object_to_array($order));
        } catch (SoapFault $fault) {
            print_r($this->client->__getLastRequest() . "<hr />" . $fault->getMessage());
            die();
        }
        
        return $return;
    }

    /**
     * Use to get Addresses based on NationalIdNumber or orgnr. Only in SE, NO, DK.
     * @param type $request Object containing SveaAuth, IsCompany, CountryCode, SecurityNumber
     * @return GetCustomerAddressesResponse Object. 
     */
    public function GetAddresses($request) {
        $builder = new SveaSoapArrayBuilder();
        try {
            $return = $this->client->GetAddresses($builder->object_to_array($request));
        } catch (SoapFault $fault) {
            print_r($this->client->__getLastRequest() . "<hr />" . $fault->getMessage());
            die();
        }
        return $return;
    }

    /**
     * Use to get params om partpayment options
     * @param type SveaAuth Object
     * @return CampaignCodeInfo Object
     */
    public function GetPaymentPlanParamsEu($auth) {
        $builder = new SveaSoapArrayBuilder();
        try {
            $return = $this->client->GetPaymentPlanParamsEu($builder->object_to_array($auth));
        } catch (SoapFault $fault) {
            print_r($this->client->__getLastRequest() . "<hr />" . $fault->getMessage());
            die();
        }
        return $return;
    }

    /**
     * 
     * @param type $deliverdata Object containing SveaAuth and DeliverOrderInformation
     * @return DeliverOrderResult Object
     */
    public function DeliverOrderEu($deliverdata) {
        $builder = new SveaSoapArrayBuilder();
        try {
            $return = $this->client->DeliverOrderEu($builder->object_to_array($deliverdata));
        } catch (SoapFault $fault) {
            print_r($this->client->__getLastRequest() . "<hr />" . $fault->getMessage());
            die();
        }
        return $return;
    }

    public function CloseOrderEu($closedata) {
        $builder = new SveaSoapArrayBuilder();
        try {
            $return = $this->client->CloseOrderEu($builder->object_to_array($closedata));
        } catch (SoapFault $fault) {
            print_r($this->client->__getLastRequest() . "<hr />" . $fault->getMessage());
            die();
        }
        return $return;
    }

}

?>