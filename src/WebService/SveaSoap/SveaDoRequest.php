<?php

namespace Svea\WebPay\WebService\SveaSoap;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationProvider;

/**
 * Create SoapObject
 * Do request
 * - return Response Object
 */
class SveaDoRequest
{
    private $svea_server;
    private $client;

    public $result;

    /**
     * Constructor, sets up soap server and SoapClient
     * @param ConfigurationProvider $config
     * @param string $ordertype -- see Svea\WebPay\Config\ConfigurationProvider:: constants
     * @param string $method Method to call by soap
     * @param object $object Object to pass in soap call
     * @param bool $logging
     */
    public function __construct($config, $ordertype, $method, $object, $logging = false)
    {
        $this->svea_server = $config->getEndPoint($ordertype);
        $this->client = $this->SetSoapClient($config);
        $this->result = $this->CallSoap($method, $object, $logging);
    }

    private function CallSoap($method, $order, $logging)
    {
        $builder = new SveaSoapArrayBuilder();
        $headers = new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', str_replace("/SveaWebPay.asmx?WSDL", "",$this->svea_server) . "/webpay/" . $method);
        $this->client->__setSoapHeaders($headers);
        $params = $builder->object_to_array($order);
        if($logging == true)
        {
            $timestampStart = time();
            $microtimeStart = microtime(true);
        }
        $result = array("requestResult" => $this->client->__soapCall($method, array($params)));
        if($logging == true)
        {
            $logs = array(
                "logs" => array(
                    "request" => array(
                        "timestamp" => $timestampStart,
                        "headers" => $this->client->__getLastRequestHeaders(),
                        "body" => htmlentities($this->client->__getLastRequest())
                    ),
                    "response" => array(
                        "timestamp" => time(),
                        "headers" => $this->client->__getLastResponseHeaders(),
                        "body" => htmlentities($this->client->__getLastResponse()),
                        "dataAmount" => strlen($this->client->__getLastResponseHeaders()) + strlen($this->client->__getLastResponse()),
                        "duration" => round(microtime(true) - $microtimeStart, 3)
                    )
                ));
            $result = array_merge($result, $logs);
        }
        return $result;
    }

    private function SetSoapClient($config)
    {
        $libraryProperties = Helper::getSveaLibraryProperties();
        $libraryName = $libraryProperties['library_name'];
        $libraryVersion = $libraryProperties['library_version'];

        $integrationProperties = Helper::getSveaIntegrationProperties($config);
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
                ))),
                "soap_version" => SOAP_1_2
            )
        );
        return $client;
    }
}