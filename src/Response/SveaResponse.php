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
     * The constructor accepts the returned Svea service response. $message, and
     * returns an instance of the corresponding service response class which
     * parses $message and sets any returned attributes, along with the common
     * response attributes $accepted, $resultcode and $errormessage.
     *
     * If the $method parameter is set, it is used to determind the service
     * type, if not, we check $message itself to see if the service response
     * has come in as a SimpleXMLElement object (i.e. a WebService response), or
     * a raw xml string (i.e. a HostedService response).
     *
     * The resulting parsed response attributes are available for inspection
     * through the getResponse() method. Inspect the individual response using
     * i.e. $myInstanceOfSveaResponse->getResponse()->theAttributeInQuestion
     *
     * @param mixed $message  contains the Svea service response
     * @param string $countryCode  needed along with $config to decode response
     * @param SveaConfigurationProvider  $config
     * @param string $method  set for i.e. HostedAdmin, AdminService requests
     * @return mixed  one of the various service request response classes
     */
    public function __construct($message, $countryCode, $config = NULL, $method = NULL) {

        // WebService requests get a stdClass object back from the SoapClient instance
        if (is_object($message)) {

            // Web Service EU responses
            if (property_exists($message, "CreateOrderEuResult")) {
                $this->response = new Svea\WebService\CreateOrderResponse($message);
            }
            elseif (property_exists($message, "GetAddressesResult")) {  // also legacy getAddresses result
                $this->response = new Svea\WebService\GetAddressesResponse($message);
            }
            elseif (property_exists($message, "GetPaymentPlanParamsEuResult")) {
                $this->response = new Svea\WebService\PaymentPlanParamsResponse($message);
            }
            elseif (property_exists($message, "DeliverOrderEuResult")) {
                $this->response = new Svea\WebService\DeliverOrderResult($message);
            }
            elseif (property_exists($message, "CloseOrderEuResult")) {
                $this->response = new Svea\WebService\CloseOrderResult($message);
            }

            // $method is set for i.e. AdminService requests
            elseif( isset($method) ) {

                switch( $method ) {
                    case "CancelOrder":
                        $this->response = new Svea\AdminService\CancelOrderResponse( $message );
                        break;
                    case "DeliverOrders":
                        $this->response = new Svea\AdminService\DeliverOrdersResponse( $message );
                        break;
                    case "GetOrders":
                        $this->response = new Svea\AdminService\GetOrdersResponse( $message );
                        break;
                    case "CancelOrderRows":
                        $this->response = new Svea\AdminService\CancelOrderRowsResponse( $message );
                        break;
                    case "AddOrderRows":
                        $this->response = new Svea\AdminService\AddOrderRowsResponse( $message );
                        break;
                    case "UpdateOrderRows":
                        $this->response = new Svea\AdminService\UpdateOrderRowsResponse( $message );
                        break;
                    case "UpdateOrder":
                        $this->response = new Svea\AdminService\UpdateOrderResponse( $message );
                        break;
                    case "CreditInvoiceRows":
                        $this->response = new Svea\AdminService\CreditInvoiceRowsResponse( $message );
                        break;
                    case "DeliverPartial":
                        $this->response = new Svea\AdminService\DeliverPartialResponse( $message );
                        break;

                    default:
                        throw new Exception("unknown method: $method");
                        break;
                }
            }

            // legacy fallback -- webservice from hosted_admin -- used by preparedpayment
            elseif (property_exists($message, "message"))   {
                 $this->response = new Svea\HostedService\HostedAdminResponse($message,$countryCode,$config);
            }
        }

        // webservice hosted payment
        elseif ($message != NULL) {
            $this->response = new Svea\HostedService\HostedPaymentResponse($message,$countryCode,$config);
        }
        else {
            $this->response = "Response is not recognized.";
        }
    }

    /**
     * Returns an instance of the corresponding service response object class (see constructor above)
     *
     * @return mixed
     */
    public function getResponse() {
        return $this->response;
    }
}
