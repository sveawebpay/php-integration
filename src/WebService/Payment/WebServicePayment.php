<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/* *
 * Parent to InvoicePayment and PaymentPlanPaymentHandles class
 * Prepares and sends $order with php SOAP
 * Uses svea_soap package to build object formatted for SveaWebPay Europe Web service API
 * Object is sent with SveaDoPayment class in svea_soap package by PHP SoapClient
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 *
 */
class WebServicePayment {

    public $order;
    public $requestObject;

    public function __construct($order) {
        $this->order = $order;
    }

    private function getPasswordBasedAuthorization() {
        $auth = new WebServiceSoap\SveaAuth();
        $auth->Username = $this->order->conf->getUsername($this->orderType,  $this->order->countryCode);
        $auth->Password = $this->order->conf->getPassword($this->orderType,  $this->order->countryCode);
        $auth->ClientNumber = $this->order->conf->getClientNumber($this->orderType,  $this->order->countryCode);
        return $auth;
    }

    public function validateOrder() {
        $this->order->orderType = $this->orderType;
         $validator = new \Svea\WebServiceOrderValidator();
         $errors = $validator->validate($this->order);
         return $errors;
    }

    /**
     * Rebuild $order with svea_soap package to be in right format for SveaWebPay Europe Web service API
     * @return prepared SveaRequest
     * @throws \Svea\ValidationException
     */
    public function prepareRequest() {
        // validate order, throw exception on validation failure
        $errors = $this->validateOrder();
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }
            throw new \Svea\ValidationException($exceptionString);
        }

        // create soap order object, set authorization
        $sveaOrder = new WebServiceSoap\SveaOrder;
        $sveaOrder->Auth = $this->getPasswordBasedAuthorization();
        //make orderrows and put in CreateOrderInfromation
        $orderinformation = $this->formatOrderInformationWithOrderRows($this->order->orderRows);

        //parallel ways of creating customer
        if (isset($this->order->customerIdentity)) {
            $orderinformation->CustomerIdentity = $this->formatCustomerDetails();
        } else {
            $orderinformation->CustomerIdentity = $this->formatCustomerIdentity();
        }

        $orderinformation->ClientOrderNumber = $this->order->clientOrderNumber;
        $orderinformation->OrderDate = $this->order->orderDate;
        $orderinformation->CustomerReference = $this->order->customerReference;
        $sveaOrder->CreateOrderInformation = $this->setOrderType($orderinformation);

        $object = new WebServiceSoap\SveaRequest();
        $object->request = $sveaOrder;

        //do request
        $this->requestObject = $object;

        return $object;
    }

    /**
     * Transforms object to array and sends it to SveaWebPay Europe Web service API by php SoapClient
     * @return CreateOrderEuResponse
     * @throws \Svea\ValidationException
     */
    public function doRequest() {

        $object = $this->prepareRequest();
        $request = new WebServiceSoap\SveaDoRequest($this->order->conf, $this->orderType);
        $svea_req = $request->CreateOrderEu($object);

        $response = new \SveaResponse($svea_req,"");
        return $response->getResponse();
    }

    /**
     * Format Order row with svea_soap package and calculate vat
     * @param type $rows
     * @return \SveaCreateOrderInformation
     */
    protected function formatOrderInformationWithOrderRows($rows) {
        $orderInformation = new WebServiceSoap\SveaCreateOrderInformation(
                (isset($this->order->campaignCode) ? $this->order->campaignCode : ""),
                (isset($this->order->sendAutomaticGiroPaymentForm) ? $this->order->sendAutomaticGiroPaymentForm : 0)
            )
        ;

        // rewrite order rows to soap_class order rows
        $formatter = new WebServiceRowFormatter($this->order);
        $formattedOrderRows = $formatter->formatRows();

        foreach ($formattedOrderRows as $formattedOrderRow) {
            $orderInformation->addOrderRow($formattedOrderRow);
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
        if (isset($this->order->orgNumber)||isset($this->order->companyVatNumber)) {
            $isCompany = true;
            $companyId = isset($this->order->orgNumber) ? $this->order->orgNumber : $this->order->companyVatNumber;
        }

        //For european countries Individual/Company - identity required
        $idValues = array();

        if ($this->order->countryCode != 'SE'
                && $this->order->countryCode != 'NO'
                && $this->order->countryCode != 'FI'
                && $this->order->countryCode != 'DK') {
            $euIdentity = new WebServiceSoap\SveaIdentity($isCompany);

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

        $individualCustomerIdentity = new WebServiceSoap\SveaCustomerIdentity($idValues);
        //For nordic countries NationalIdNumber is required
        if ($this->order->countryCode != 'NL' && $this->order->countryCode != 'DE') {
            //set with companyVatNumber for Company and NationalIdNumber for individual
            $individualCustomerIdentity->NationalIdNumber = $isCompany ? $companyId : $this->order->ssn;
        }

        if ($isCompany) {
            $individualCustomerIdentity->FullName = isset($this->order->companyName) ? $this->order->companyName : "";
        } else {
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
        $individualCustomerIdentity->PublicKey = isset($this->order->publicKey) ? $this->order->publicKey : "";

        return $individualCustomerIdentity;
    }

    /**
     * if CustomerIdentity is created by addCustomerDetails()
     * @return \SveaCustomerIdentity
     */
    public function formatCustomerDetails() {
        $isCompany = false;
        get_class($this->order->customerIdentity) == "Svea\CompanyCustomer" ? $isCompany = TRUE : $isCompany = FALSE;

        $companyId ="";
        if (isset($this->order->customerIdentity->orgNumber)||isset($this->order->customerIdentity->companyVatNumber)) {
            $isCompany = true;
            $companyId = isset($this->order->customerIdentity->orgNumber) ? $this->order->customerIdentity->orgNumber : $this->order->customerIdentity->companyVatNumber;
        }

        //For european countries Individual/Company - identity required
        $idValues = array();

        if ($this->order->countryCode != 'SE'
                && $this->order->countryCode != 'NO'
                && $this->order->countryCode != 'FI'
                && $this->order->countryCode != 'DK') {
            $euIdentity = new WebServiceSoap\SveaIdentity($isCompany);

            if ($isCompany) {
                $euIdentity->CompanyVatNumber = $companyId;
            } else {
                $euIdentity->FirstName = $this->order->customerIdentity->firstname;
                $euIdentity->LastName = $this->order->customerIdentity->lastname;
                if ($this->order->countryCode == 'NL') {
                    $euIdentity->Initials = $this->order->customerIdentity->initials;
                }
                $euIdentity->BirthDate = $this->order->customerIdentity->birthDate;
            }

            $type = ($isCompany ? "CompanyIdentity" : "IndividualIdentity");
            $idValues[$type] = $euIdentity;
        }

        $individualCustomerIdentity = new WebServiceSoap\SveaCustomerIdentity($idValues);
        //For nordic countries NationalIdNumber is required
        if ($this->order->countryCode != 'NL' && $this->order->countryCode != 'DE') {
            //set with companyVatNumber for Company and NationalIdNumber for individual
            $individualCustomerIdentity->NationalIdNumber = $isCompany ? $companyId : $this->order->customerIdentity->ssn;
        }

        if ($isCompany) {
            $individualCustomerIdentity->FullName = isset($this->order->customerIdentity->companyName) ? $this->order->customerIdentity->companyName : "";
        } else {
            $individualCustomerIdentity->FullName = isset($this->order->customerIdentity->firstname) && isset($this->order->customerIdentity->lastname) ? $this->order->customerIdentity->firstname. ' ' .$this->order->customerIdentity->lastname : "";
        }

        $individualCustomerIdentity->PhoneNumber = isset($this->order->customerIdentity->phonenumber) ? $this->order->customerIdentity->phonenumber : "";
        $individualCustomerIdentity->Street = isset($this->order->customerIdentity->street) ? $this->order->customerIdentity->street : "";
        $individualCustomerIdentity->HouseNumber = isset($this->order->customerIdentity->housenumber) ? $this->order->customerIdentity->housenumber : "";
        $individualCustomerIdentity->CoAddress = isset($this->order->customerIdentity->coAddress) ? $this->order->customerIdentity->coAddress : "";
        $individualCustomerIdentity->ZipCode = isset($this->order->customerIdentity->zipCode) ? $this->order->customerIdentity->zipCode : "";
        $individualCustomerIdentity->Locality = isset($this->order->customerIdentity->locality) ? $this->order->customerIdentity->locality : "";
        $individualCustomerIdentity->Email = isset($this->order->customerIdentity->email) ? $this->order->customerIdentity->email : "";
        $individualCustomerIdentity->IpAddress = isset($this->order->customerIdentity->ipAddress) ? $this->order->customerIdentity->ipAddress : "";

        $individualCustomerIdentity->CountryCode = $this->order->countryCode;
        $individualCustomerIdentity->CustomerType = $isCompany ? "Company" : "Individual";
        $individualCustomerIdentity->PublicKey = isset($this->order->customerIdentity->publicKey) ? $this->order->customerIdentity->publicKey : "";

        return $individualCustomerIdentity;
    }

    /**
     * Get calculated totals before sending the request
     * @return Array of the rounded sums of all orderrows as it will be sent to Svea
     */
    public function getRequestTotals() {
        $object = $this->prepareRequest();
        $total_incvat = 0;
        $total_exvat = 0;
        $total_vat = 0;
        foreach ($object->request->CreateOrderInformation->OrderRows['OrderRow'] as $value) {
            $rowExVat = $this->calculateOrderRowExVat($value);
            $total_exvat += $rowExVat;
            $rowVat = $this->calculateTotalVatSumOfRows($value);
            $total_vat += $rowVat;
            $total_incvat += \Svea\Helper::bround(($rowExVat + $rowVat),2);
        }
        return array('total_exvat' => $total_exvat, 'total_incvat' => $total_incvat, 'total_vat' => $total_vat);


    }

    private function calculateOrderRowExVat($row) {
        if ($row->PriceIncludingVat == true) {
            $rowsum_incvat = $this->getRowAmount( $row );
            $rowsum_exvat = $this->convertIncVatToExVat( $row, $rowsum_incvat );
        } else {
            $rowsum_exvat = $this->getRowAmount( $row );
        }
        return \Svea\Helper::bround($rowsum_exvat,2);
    }

    private function convertIncVatToExVat( $row, $rowsum_incvat ) {
        return \Svea\Helper::bround( ($rowsum_incvat / (1 + ($row->VatPercent / 100))),2);
    }

    private function getRowAmount( $row ) {
        return \Svea\Helper::bround($row->NumberOfUnits,2) *
               \Svea\Helper::bround($row->PricePerUnit,2) *
               (1 - ($row->DiscountPercent / 100));
    }

    private function calculateTotalVatSumOfRows($row) {
        //if amount inc vat
        $sum = 0;
        //calculate the exvat sum
        if ($row->PriceIncludingVat == true) {
            $rowsum_incvat = $this->getRowAmount( $row );
            $exvat = $this->convertIncVatToExVat( $row, $rowsum_incvat );

            $vat = \Svea\Helper::bround($rowsum_incvat,2) - \Svea\Helper::bround($exvat,2);
            $sum += $vat;
        } else {
            $exvat = $this->getRowAmount( $row );

            $vat = \Svea\Helper::bround($exvat,2) * ($row->VatPercent / 100 );
            $sum += \Svea\Helper::bround($vat,2);
        }
//        $vat = \Svea\Helper::bround($exvat,2) * ($row->VatPercent / 100 );
//        $sum += intval(100.00 * $vat) / 100.00; //php for .NET Math.Truncate -- round to nearest integer towards zero

        return $sum;
    }
}
