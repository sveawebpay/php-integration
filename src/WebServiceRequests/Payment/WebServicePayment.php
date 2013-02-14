<?php

require_once '/../svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/* *
 * Description of WebServicePayment
 * Parent to InvoicePayment and PaymentPlanPaymentHandles class
 * Prepares and sends $order with php SOAP
 * Uses svea_soap package to build object formatted for SveaWebPay Europe Web service API
 * Object is sent with SveaDoPayment class in svea_soap package by PHP SoapClient
 * @package WebServideRequests/Payment
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 *
 */
class WebServicePayment {

    public $order;
    public $requestObject;

    public function __construct($order) {
        $this->order = $order;
    }
    
    /**
     * Alternative drop or change file in Config/SveaConfig.php
     * Note! This fuction may change in future updates.
     * @param type $merchantId
     * @param type $secret
     */
    public function setPasswordBasedAuthorization($username, $password, $clientNumber) {
        $this->order->conf->username = $username;
        $this->order->conf->password = $password;
        if ($this->orderType == "Invoice") {
            $this->order->conf->invoiceClientnumber = $clientNumber;
        } else {
            $this->order->conf->paymentPlanClientnumber = $clientNumber;
        }
        return $this;
    }

    private function getPasswordBasedAuthorization() {
        $authArray = $this->order->conf->getPasswordBasedAuthorization($this->orderType);
        $auth = new SveaAuth();
        $auth->Username = $authArray['username'];
        $auth->Password = $authArray['password'];
        $auth->ClientNumber = $authArray['clientnumber'];
        return $auth;
    }

    public function validateOrder(){
         $validator = new WebServiceOrderValidator();
         $errors = $validator->validate($this->order);
         return $errors;
    }

        /**
     * Rebuild $order with svea_soap package to be in right format for SveaWebPay Europe Web service API
     * @return prepared SveaRequest
     */
    public function prepareRequest() {
        $errors = $this->validateOrder();       
        if(count($errors) > 0){
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";                
            }
           
            throw new ValidationException($exceptionString);
        }
        $sveaOrder = new SveaOrder;
        $sveaOrder->Auth = $this->getPasswordBasedAuthorization();
        //make orderrows and put in CreateOrderInfromation
        $orderinformation = $this->formatOrderInformationWithOrderRows($this->order->orderRows);
        $orderinformation->CustomerIdentity = $this->formatCustomerIdentity();
        $orderinformation->ClientOrderNumber = $this->order->clientOrderNumber;
        $orderinformation->OrderDate = $this->order->orderDate;
        $orderinformation->CustomerReference = $this->order->customerReference;
        $sveaOrder->CreateOrderInformation = $this->setOrderType($orderinformation);

        $object = new SveaRequest();
        $object->request = $sveaOrder;

        //do request
        $this->requestObject = $object;

        return $object;
    }

    /**
     * Transforms object to array and sends it to SveaWebPay Europe Web service API by php SoapClient
     * @return CreateOrderEuResponse
     */
    public function doRequest() {
        $object = $this->prepareRequest();
        $url = $this->order->testmode ? SveaConfig::SWP_TEST_WS_URL : SveaConfig::SWP_PROD_WS_URL;
        $request = new SveaDoRequest($url);
        $svea_req = $request->CreateOrderEu($object);
     
        $response = new SveaResponse($svea_req);
        return $response->response;
    }

    /**
     * Format Order row with svea_soap package and calculate vat
     * @param type $rows
     * @return \SveaCreateOrderInformation
     */
    protected function formatOrderInformationWithOrderRows($rows) {
        $orderInformation = new SveaCreateOrderInformation((isset($this->order->campaignCode) ? $this->order->campaignCode : ""),
                        (isset($this->order->sendAutomaticGiroPaymentForm) ? $this->order->sendAutomaticGiroPaymentForm : 0));

        $formatter = new WebServiceRowFormatter($this->order);
        $formattedOrderRows = $formatter->formatRows();

        foreach ($formattedOrderRows as $orderRow) {
            $orderInformation->addOrderRow($orderRow);
        }

        return $orderInformation;
    }

    /**
     * Format Customer Identity with svea_soap package
     * @return \SveaCustomerIdentity
     */ 
    private function formatCustomerIdentity() {
        $isCompany = false;
        $companyId ="";
        if(isset($this->order->orgNumber)||isset($this->order->companyVatNumber)){
            $isCompany = true;
            $companyId = isset($this->order->orgNumber) ? $this->order->orgNumber : $this->order->companyVatNumber;
        }
     
        //For european countries Individual/Company - identity required
        $idValues = array();
        
        if ($this->order->countryCode != 'SE'
                && $this->order->countryCode != 'NO'
                && $this->order->countryCode != 'FI'
                && $this->order->countryCode != 'DK') {
            $euIdentity = new SveaIdentity($isCompany);
            
            if ($isCompany) {
                $euIdentity->CompanyVatNumber = $companyId;
            } else {
                $euIdentity->FirstName = $this->order->firstname;
                $euIdentity->LastName = $this->order->lastname;
                if ($this->order->countryCode == 'NL') {
                    $euIdentity->Initials = $this->order->initials;
                }
                $euIdentity->BirthDate = $this->order->birthDate;
            }
            
            $type = ($isCompany ? "CompanyIdentity" : "IndividualIdentity");
            $idValues[$type] = $euIdentity;
        }

        $individualCustomerIdentity = new SveaCustomerIdentity($idValues);
        //For nordic countries NationalIdNumber is required
        if ($this->order->countryCode != 'NL' && $this->order->countryCode != 'DE') {
            //set with companyVatNumber for Company and ssn for individual
            $individualCustomerIdentity->NationalIdNumber = $isCompany ? $companyId : $this->order->ssn;
        }
        
        if ($isCompany) {
            $individualCustomerIdentity->FullName = isset($this->order->companyName) ? $this->order->companyName : "";         
        }  else {
            $individualCustomerIdentity->FullName = isset($this->order->firstname) && isset($this->order->lastname) ? $this->order->firstname. ' ' .$this->order->lastname : ""; 
        }
        
        $individualCustomerIdentity->PhoneNumber = isset($this->order->phonenumber) ? $this->order->phonenumber : "";
        $individualCustomerIdentity->Street = isset($this->order->street) ? $this->order->street : "";
        $individualCustomerIdentity->HouseNumber = isset($this->order->housenumber) ? $this->order->housenumber : "";
        $individualCustomerIdentity->CoAddress = isset($this->order->coAddress) ? $this->order->coAddress : "";
        $individualCustomerIdentity->ZipCode = isset($this->order->zipCode) ? $this->order->zipCode : "";
        $individualCustomerIdentity->Locality = isset($this->order->locality) ? $this->order->locality : "";
        $individualCustomerIdentity->Email = isset($this->order->email) ? $this->order->email : "";
        $individualCustomerIdentity->IpAddress = isset($this->order->ipAddress) ? $this->order->ipAddress : "";

        $individualCustomerIdentity->CountryCode = $this->order->countryCode;
        $individualCustomerIdentity->CustomerType = $isCompany ? "Company" : "Individual";

        return $individualCustomerIdentity;
    }
}

?>
