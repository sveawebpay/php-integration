<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class SearchOrdersRequest
{
    public $Authentication;     // note that the order of the attributes matter!
    public $AccountCreditsToRetrieve;

    /**
     * AdminService GetOrdersRequest
     * @param Authentication $authentication
     * @param OrdersToRetrieve $ordersToRetrieve
     */
    function __construct($authentication, $accountsToRetrieve)
    {
        $this->Authentication = new SoapVar($authentication, SOAP_ENC_OBJECT,
            "-", "--", "Authentication", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package

        $list = array();
        foreach($accountsToRetrieve as $accountInfo)
        {
            $clientAccountCreditId = new SoapVar($accountInfo->clientAccountCreditId, XSD_LONG,
                "-", "--", "ClientAccountCreditId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Account");

            $clientId = new SoapVar($accountInfo->clientId, XSD_LONG,
                "-", "--", "ClientId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Account");

            $obj = new \stdClass();
            $obj->ClientAccountCreditId = $clientAccountCreditId;
            $obj->ClientId = $clientId;


            $list [] = new SoapVar($obj, SOAP_ENC_OBJECT,
                "-", "--", "GetAccountCreditInformation", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Account");
        }

        $this->AccountCreditsToRetrieve = new SoapVar($list, SOAP_ENC_OBJECT,
            "-", "--", "AccountCreditsToRetrieve", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}
