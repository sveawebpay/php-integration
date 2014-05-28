<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * SveaResponse creates a uniform response object from a call to Svea services.
 * 
 * SveaResponse returns an instance of the response class corresponding to 
 * the request sent to Svea, i.e. instances of subclasses to HostedResponse 
 * and WebServiceResponse, respectively.
 * 
 * For asynchronous services, create an instance of SveaResponse, pass it the 
 * resulting xml response as part of the $_REQUEST response along with 
 * countryCode and config, then receive your HostedResponse instance by calling 
 * the getResponse() method. 
 * 
 * For synchronous services, the appropriate WebServiceResponse instance is 
 * returned by calling ->doRequest() on the order object.
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class SveaResponse {

    /**
     * @deprecated, use SveaResponse->getResponse() to access the $response object directly
     * @var public $response, instance of HostedResponse or WebServiceResponse
     */
    public $response;

    /**
     * The constructor checks the parameter $message to see if the service response
     * has come in as a SimpleXMLElement object or as a raw xml string. Then it
     * creates the appropriate Response object which parses the response and does
     * error handling et al.
     * 
     * The resulting parsed response attributes are available for inspection 
     * through the getResponse() method. Inspect the individual response using
     * i.e. $myInstanceOfSveaResponse->getResponse()->serviceResponseAttribute
     * 
     * @param SimpleXMLElement|string  $message contains the Svea service response, either as an object or as raw xml (for hosted payments)
     * @param string $countryCode
     * @param SveaConfigurationProvider $config
     * @param string $method  set for HostedAdminRequests, indicates the request method used  
     * @return mixed instance of a subclass to HostedResponse or WebServiceResponse, respectively
     */
    public function __construct($message, $countryCode, $config = NULL, $method = NULL) {
        
        // WebService requests get a stdClass object back from the SoapClient instance
        if (is_object($message)) {

            // TODO fix it so that these too look at $method instead of using property_exists()
            
            // Web Service EU responses
            if (property_exists($message, "CreateOrderEuResult")) {
                $this->response = new Svea\CreateOrderResponse($message);
            } 
            elseif (property_exists($message, "GetAddressesResult")) {
                $this->response = new Svea\GetAddressesResponse($message);
            } 
            elseif (property_exists($message, "GetPaymentPlanParamsEuResult")) {
                $this->response = new Svea\PaymentPlanParamsResponse($message);
            } 
            elseif (property_exists($message, "DeliverOrderEuResult")) {
                $this->response = new Svea\DeliverOrderResult($message);
            } 
            elseif (property_exists($message, "CloseOrderEuResult")) {
                $this->response = new Svea\CloseOrderResult($message);
            }
            
            elseif( isset($method) ) {                
                switch( $method ) {

                    // $method is set for Admin Web Service requests
                    case "CancelOrder":
                        $this->response = new Svea\CancelOrderResponse( $message );
                        break;                    
                    case "DeliverOrders":
                        $this->response = new Svea\DeliverOrdersResponse( $message );
                        break;
                    case "GetOrders":
                        $this->response = new Svea\GetOrdersResponse( $message );
                        break;
                    case "CancelOrderRows":
                        $this->response = new Svea\CancelOrderRowsResponse( $message );
                        break;                    
                    case "AddOrderRows":
                        $this->response = new Svea\AddOrderRowsResponse( $message );
                        break;
                    case "UpdateOrderRows":
                        $this->response = new Svea\UpdateOrderRowsResponse( $message );
                        break;
                                            
                    // $method is set for HostedAdminRequests, indicates the request method used                    
                    case "querytransactionid":
                        $this->response = new Svea\QueryTransactionResponse($message, $countryCode, $config);
                        break;
                    case "annul":
                        $this->response = new Svea\AnnulTransactionResponse($message, $countryCode, $config);
                        break;                     
                    case "credit":
                        $this->response = new Svea\CreditTransactionResponse($message, $countryCode, $config);
                        break;                       
                    case "confirm":
                        $this->response = new Svea\ConfirmTransactionResponse($message, $countryCode, $config);
                        break;   
                    case "loweramount":
                        $this->response = new Svea\LowerTransactionResponse($message, $countryCode, $config);
                        break;    
                    case "recur":
                        $this->response = new Svea\RecurTransactionResponse($message, $countryCode, $config);
                        break;    
                    case "getpaymentmethods":
                        $this->response = new Svea\ListPaymentMethodsResponse($message, $countryCode, $config);
                        break;
                    
                    default:
                        throw new Exception("unknown method: $method");
                        break;
                }
            }
            // legacy fallback -- webservice from hosted_admin -- used by preparedpayment 
            elseif (property_exists($message, "message"))   {                
                 $this->response = new Svea\HostedAdminResponse($message,$countryCode,$config);
            }
        } 
        elseif ($message != NULL) {
            $this->response = new Svea\HostedPaymentResponse($message,$countryCode,$config);
        } 
        else {
            $this->response = "Response is not recognized.";
        }
    }
    
    /**
     * Returns an instance of the corresponding response object class 
     * (see constructor above)
     *
     * @return mixed 
     */
    public function getResponse() {
        return $this->response;
    }
}
