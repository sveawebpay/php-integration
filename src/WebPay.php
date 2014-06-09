<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * The WebPay and WebPayItem class methods make up the Svea WebPay API and 
 * provide unified entrypoints to the various Svea web services. The API also
 * encompass the support classes WebPayItem and ConfigurationProvider, as well 
 * as various constant container classes.
 * 
 * WebPay:: methods contains the functions needed to create orders and perform 
 * payment requests using all four Svea payment methods. It contains functions 
 * to define order contents, send order requests, as well as support functions 
 * needed to do this.
 * 
 * WebPayAdmin:: methods are used to administrate orders after they have been
 * accepted by Svea. It includes functions to update, deliver, cancel and credit
 * orders et.al.
 * 
 * See the provided README.md file for an explanation of how to utilise the
 * WebPay and WebPayAdmin classes. The complete WebPay Integration package, as 
 * well as the underlying Svea service classes, methods and structures is also 
 * documented by generated documentation, see the apidoc folder.   
 * 
 * The underlying services and methods are contained in the Svea namespace, and
 * may be accessed, though their api and interfaces are subject to change. These
 * are documented through the generated documentation.
 * 
 * BETA release 2.0b (140515) 
 * Not all functionality has yet been implemented as WebPay/WebPayAdmin methods. See below.
 * 
 * METHOD OVERVIEW:
 * (methods in parenthesises are deprecated), *starred methods are new to 2.0, **double starred methods are not yet implemented in release 2.0b
 * 
 * 
 * WebPay:: 
 *  createOrder  -- create order and pay via invoice, payment plan, card, or direct bank payment methods
 *  (deliverOrder) (with orderRows)-- partially deliver, change or credit invoice, payment plan orders depending on set options
 *  *deliverOrder (without orderRows) -- deliver in full invoice, payment plan orders, confirms card orders 
 *  (closeOrder) -- cancel non-delivered invoice or payment plan
 *  getAddresses -- fetch addresses connected with a provided customer identity
 *  getPaymentMethods -- fetch available payment methods for a clientid, used by i.e. direct bank orders
 *  getPaymentPlanParams -- fetch current campaigns (payment plan params) for a clientid, used by paymentplan orders
 *  getPaymentPlanPricePerMonth -- calculates price per month over all available campaigns for a specified amount 
 * 
 *  WebPayAdmin::
 *  cancelOrder -- cancel in whole non-delivered invoice or payment plan orders, or annul non-confirmed card orders
 *  queryOrder -- get information about an order, including numbered order rows for invoice or payment plan orders
 * 
 *  cancelOrderRows -- cancel order rows in non-delivered invoice or payment plan order, or lower amount to charge (only) for non-confirmed card orders
 * 	->cancelInvoiceOrderRows(): use admin service CancelOrderRows with given numbered order rows to cancel order row
 * 	->cancelPaymentPlanOrderRows(): as for invoice
 * 	->cancelCardOrderRows: use LowerTransaction with given numbered order rows to lower the amount to charge by the order row amount; note that  * rder rows won’t change, just the order total
 * 	->cancelDirectBankOrderRows: not supported
 * 
 *  addOrderRows -- add order rows to non-delivered invoice or payment plan order
 * 	->addInvoiceOrderRows(): use admin service AddOrderRows with given Svea\OrderRow objects to add order rows
 * 	->addPaymentPlanOrderRows(): as for invoice above
 *	->addCardOrderRows(): – not supported
 * 	->addDirectBankOrderRows(): -  not supported
 * 
 * updateOrderRows -- update order rows in non-delivered invoice or payment plan order, or lower amount to charge (only) for non-confirmed card orders
 * 	->updateInvoiceOrderRows(): use admin service UpdateOrderRows with given (numbered order row, Svea\OrderRow object) pairs to update order rows
 * 	->updatePaymentPlanOrderRows(): as for invoice
 * 	->updateCardOrderRows(): -- only possible to lower the amount for an order, method should ensure this.
 * 	->updateDirectBankRows(): -- not supported
 * 
 * 	Implement admin service UpdateOrderRows
 * 	Create AdminSoap classes
 * 	Create UpdateOrderRowsResult class
 * 	Create UpdateOrderRowsBuilder class
 * 	->updateOrderRows( int:numberedOrderRow, Svea\OrderRow:updatedOrderRow)
 * 	->setOrderId()
 * 	Validation of OrderBuilder attributes needed to place request
 * 
 * 	Card: check if amount is <= current amount, or return error message
 * 	Card: do LowerTransaction request
 * 
 *  **creditOrderRows -- credit order rows in delivered invoice or payment plan order, or credit confirmed card orders
 * 	->creditInvoiceOrderRows(): use admin service CreditInvoiceRows with given numbered order rows to credit order rows, should return  * nvoicenumber of creditinvoice
 * 	->creditPaymentPlanOrderRows(): as for invoice above
 * 	->creditCardOrderRows(): use CreditTransaction with given numbered order rows to credit the order row amount; note that order rows won’t change just the order total
* 	->creditDirectBankOrderRows: as for card above
 * 
 * 	Implement admin service CreditInvoiceRows
 * 	Create AdminSoap classes
 * 	Create CreditOrderRowsResult class
 * 	Create creditOrderRowsBuilder class
 * 	->creditOrderRows( int:numberedOrderRow )
 * 	->setOrderId()
 * 	Validation of OrderBuilder attributes needed to place request
 * 
 * 	Card: (we haven’t got state of the order, and can’t check status of individual order rows, so won’t do any validation -- document)
 * 	Card: do CreditTransaction request for the amount
 * 
 *   **listPaymentMethods -- WPA equivalent of WP::getPaymentMethods 
 * 
 *	straightforward port of existing WebPay::getPaymentMethods, but should return object instead of array
 *	create listPaymentMethodsResult class
 *
 * The following methods are provided in WebPayAdmin as a stopgap measure to perform administrative functions for card orders.
 * These entrypoints will be removed from the package in the 2.0 release, but will still be available in the Svea namespace.
 * 
 * WebPayAdmin::
 *   (annulTransaction) -- returns Svea\AnnulTransaction object, used to cancel (annul) a non-confirmed card order - use WPA::cancelOrder instead
 *   (confirmTransaction) -- returns Svea\ConfirmTransaction object, used to deliver (confirm) a non-confirmed card order - use WP::deliverOrder instead
 *   (lowerTransaction) -- returns Svea\LowerTransaction object, used to lower the amount to be charged in a non-confirmed cardOrder
 *   (creditTransaction) -- returns Svea\CreditTransaction object, used to credit confirmed card, or direct bank orders
 *   (queryTransaction) -- returns Svea\QueryTransaction object, used to get information about a card or direct bank order 
 * 
 * INNER WORKINGS (examples):
 * In general, the WebPay API starts out with creating an order object, which is then build up with data using fluid method calls. 
 * At a certain point, a method is used to select which service the order will go against. This method then returns an object of a 
 * different class, which handles the request to the service chosen. 
 * 
 * An example of this usage is the API method WebPay::createOrder()->setXX->..->useInvoicePayment(), returning an instance of the CardPayment class.
 * See the BuildOrder/CreateOrderBuilder, BuildOrder/RowBuilders/WebPayItem classes, et al.
 * 
 * It is also possible to create the service objects directly, making sure to set all relevant methods before finishing with a method to perform
 * the request to the service. In general, the objects will validate that all required attributes are present, if not, an exception will be thrown
 * stating what is missing for the service in question. 
 * 
 * Examples of these classes are HostedRequest/HandleOrder/AnnulTransaction, HostedRequest/Payment/CardPayment, 
 * WebServiceRequest/HandleOrder/CloseOrder, WebService/Payment/InvoicePayment, AdminServiceRequest/CancelOrderRequest, et al.
 * 
 * NOTES ON THE PACKAGE DESIGN:
 * This structure enables the WebPay and WebPayAdmin entrypoint methods to confine themselves to the order domain, and pushes the various service request details lower into the package stack, away from the immediate viewpoint of the integrator view. Thus all payment methods may be accessed in an uniform way, with the package doing the main work of massaging the order data to fit the various services. 
 * 
 * This also provides future compatibility, as the main WebPay and WebPayAdmin entrypoint methods stay stable whereas the details of how the services
 * are called may change.
 * 
 * That being said, there is no additional prohibiltions on using the various service call wrapper classes to access the Svea services directly, while
 * still not having to worry about the details on how to i.e. build the various SOAP calls or XML data structures. These are the classes within the 
 * Svea namespace. All service classes are documented by generated documentation included in the package. 
 * 
 * WebPay: 
 *   createOrder creates BuildOrder/orderBuilder objects containing order data
 *     -- useInvoicePayment creates an instance of WebService/Payment/InvoicePayment which does request to Svea Europe Web Service SOAP service
 *     -- useCardPayment creates and instance of HostedRequest/Payment/CardPayment which returns the xml request to send to the SveaWebPay service 
 * WebPayAdmin:
 *   cancelOrder creates a BuildOrder/cancelOrderBuilder object populated with data about the order to cancel
 *     -- cancelInvoiceOrder creates an instance of WebService/HandleOrder/CloseOrder
 *     -- cancelCardOrder creates an instance of HostedRequests/HandleOrder/AnnulTransaction
 * 
 * COMPATIBILTIY:
 * To create and administrate orders the WebPay class functions remain compatible
 * with 1.x of the integration package. Some methods have been marked as 
 * deprecated and/or moved into the new WebPayAdmin class. These will remain for
 * now, but new integrations are naturally advised to avoid using them. Alternate
 * methods are provided for most.
 * 
 * @version 2.0b
 * @package WebPay
 * @api 
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class WebPay {

    /**
     * Entry point for the order creation process.
     *
     * See CreateOrderBuilder class for more info on specifying order contents 
     * and chosing payment type, followed by sending the request to Svea and 
     * parsing the response.
     * 
     * @return Svea\CreateOrderBuilder
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @throws Exception if $config == NULL
     *
     */
    public static function createOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\CreateOrderBuilder($config);
    }

    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return Svea\GetPaymentPlanParams instance
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getPaymentPlanParams($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\WebService\GetPaymentPlanParams($config);
    }

    /**
     * Calculates price per month for all available campaigns.
     *
     * This is a helper function provided to calculate the monthly price for the
     * different payment plan options for a given sum. This information may be
     * used when displaying i.e. payment options to the customer by checkout, or
     * to display the lowest amount due per month to display on a product level.
     *
     * The returned instance contains an array value, where each element in turn
     * contains a pair of campaign code and price per month:
     * $paymentPlanParamsResonseObject->value[0..n] (for n campaignCodes), where
     * value['campaignCode' => campaignCode, 'pricePerMonth' => pricePerMonth]
     *
     * @param float $price
     * @param object $paymentPlanParamsResonseObject
     * @return Svea\PaymentPlanPricePerMonth $paymentPlanParamsResonseObject
     *
     */
    public static function paymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject) {
        return new Svea\PaymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject);
    }

    /**
     * Start Building Request Deliver Orders.
     * @return DeliverOrderBuilder object
     * @param ConfigurationProvider $config  instance of implementation class of ConfigurationProvider Interface
     */
    public static function deliverOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\DeliverOrderBuilder($config);
    }
        
    /**
     * Start building Request to close orders. Only supports Invoice or Payment plan orders.
     * @return Svea\closeOrderBuilder object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @deprecated 2.0.0 -- use WebPayAdmin::cancelOrder instead, which supports both synchronous and asynchronous orders
     */
    public static function closeOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\closeOrderBuilder($config);
    }
    
    /**
     * Start building Request for getting Address
     * @return Svea\GetAddresses object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getAddresses($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\WebService\GetAddresses($config);
    }
    
    /**
     * Get all paymentmethods connected to your account
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return string[] array of available paymentmethods for this ConfigurationProvider
     * @deprecated 2.0.0 use WebPayAdmin::listPaymentMethods instead, which returns a HostedResponse object instead of an array
     */
    public static function getPaymentMethods($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\GetPaymentMethods($config);
    }
                    
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
