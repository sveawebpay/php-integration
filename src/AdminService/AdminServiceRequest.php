<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * AdminServiceRequest is the parent of all admin webservice requests.
 *
 * @author Kristian Grossman-Madsen
 */
abstract class AdminServiceRequest {

    /** @var string $action  the AdminService soap action called by this class */
    protected $action;

    /** @var string $countryCode */
    protected $countryCode;

    /** @var boolean $priceIncludingVat */
    protected $priceIncludingVat;

    protected $resendOrderVat = NULL;

    /**
     * Set up the soap client and perform the soap call, with the soap action and prepared request from the relevant subclass
     * @return StdClass  raw response
     */
    public function doRequest() {

        $endpoint = $this->orderBuilder->conf->getEndPoint( \ConfigurationProvider::ADMIN_TYPE );   // get test or prod using child instance data
        $requestObject = $this->prepareRequest();
        if(property_exists($requestObject, 'OrderRows')) {
             $priceIncludingVat =  $requestObject->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value;
        }  elseif (property_exists($requestObject, 'UpdatedOrderRows')) {
             $priceIncludingVat =  $requestObject->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value;
        } else {
            $priceIncludingVat = $this->priceIncludingVat;
        }

        $soapClient = new AdminSoap\SoapClient( $endpoint );
        $soapResponse = $soapClient->doSoapCall($this->action, $requestObject );
        $sveaResponse = new \SveaResponse( $soapResponse, null, null, $this->action );
        $response = $sveaResponse->getResponse();
        if ($response->resultcode == "50036") {
            $this->resendOrderVat = TRUE;
            $this->priceIncludingVat = $priceIncludingVat ? FALSE : TRUE;
            $requestObject = $this->prepareRequest();
            $soapClient = new AdminSoap\SoapClient( $endpoint );
            $soapResponse = $soapClient->doSoapCall($this->action, $requestObject );
            $sveaResponse = new \SveaResponse( $soapResponse, null, null, $this->action );

        }

        return $sveaResponse->getResponse();
    }

    /**
     * Validates the orderBuilder object to make sure that all required settings
     * are present. If not, throws an exception. Actual validation is delegated
     * to subclass validate() implementations.
     *
     * @throws ValidationException
     */
    public function validateRequest() {
        // validate sub-class requirements by calling sub-class validate() method
        $errors = $this->validate();

        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $error) {
                foreach( $error as $key => $value) {
                    $exceptionString .="-". $key. " : ".$value."\n";
                }
            }

            throw new \Svea\ValidationException($exceptionString);
        }
    }

    abstract function prepareRequest(); // prepare the soap request data

    abstract function validate(); // validate is defined by subclasses, should validate all elements required for call is present

    /**
     * the integration package ConfigurationProvider::INVOICE_TYPE and ::PAYMENTPLAN_TYPE constanst are all caps, whereas the admin service
     * enumeration used in the calls are CamelCase. This function converts the package constants so they work with the admin service.
     */
    public static function CamelCaseOrderType( $orderTypeAsConst ) {
        switch( $orderTypeAsConst ) {
            case \ConfigurationProvider::INVOICE_TYPE:
                return "Invoice";
                break;
            case \ConfigurationProvider::PAYMENTPLAN_TYPE:
                return "PaymentPlan";
                break;
            default:
                return $orderTypeAsConst;
        }
    }
}
