<?php
// Svea\WebPay\WebPayAdmin class is excluded from Svea namespace

namespace Svea\WebPay;

use Svea\WebPay\AdminService\SearchOrdersRequest;
use Svea\WebPay\BuildOrder\AccountCreditQueryBuilder;
use Svea\WebPay\BuildOrder\QueryOrderBuilder;
use Svea\WebPay\BuildOrder\QueryTaskInfoBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CancelOrderBuilder;
use Svea\WebPay\BuildOrder\UpdateOrderBuilder;
use Svea\WebPay\BuildOrder\AddOrderRowsBuilder;
use Svea\WebPay\BuildOrder\CreditAmountBuilder;
use Svea\WebPay\BuildOrder\CancelOrderRowsBuilder;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder;
use Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder;
use Svea\WebPay\BuildOrder\Validator\ValidationException;

/**
 * ## Introduction
 * The Svea\WebPay\WebPay and Svea\WebPay\WebPayAdmin classes make up the Svea Svea\WebPay\WebPay API. Together they provide unified entrypoints to the various Svea web services. The API also encompass the support classes Svea\WebPay\Config\ConfigurationProvider, Svea\WebPay\Response\SveaResponse and Svea\WebPay\WebPayItem, as well as various constant container classes.
 *
 * The Svea\WebPay\WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains methods to define order contents, send order requests, as well as support methods needed to do this.
 *
 * The Svea\WebPay\WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea. It includes functions to update, deliver, cancel and credit orders et.al.
 *
 * ### Package design philosophy
 * In general, a request to Svea using the Svea\WebPay\WebPay API starts out with you creating an instance of an order builder class, which is then built up with data using fluid method calls. At a certain point, a method is used to select which service the request will go against. This method then returns an service request instance of a different class, which handles the request to the service chosen. The service request will return a response object containing the various service responses and/or error codes.
 *
 * The Svea\WebPay\WebPay API consists of the entrypoint methods in the Svea\WebPay\WebPay and Svea\WebPay\WebPayAdmin classes. These instantiate order builder classes in the Svea namespace, or in some cases request builder classes in the WebService, HostedService and AdminService sub-namespaces.
 *
 * Given an instance, you then use method calls in the respective classes to populate the order or request instance. For orders, you then choose the payment method and get a request class in return. Send the request and get a service response from Svea in return. In general, the request classes will validate that all required attributes are present, and if not throw an exception stating what is missing for the request in question.
 *
 * ### Synchronous and asynchronous requests
 * Most service requests are synchronous and return a response immediately. For asynchronous hosted service payment requests, the customer will be redirected to i.e. the selected card payment provider or bank, and you will get a callback to a return url, where where you receive and parse the response.
 *
 * ### Namespaces
 * The package makes use of PHP namespaces, grouping most classes under the namespace Svea. The entrypoint classes Svea\WebPay\WebPay, Svea\WebPay\WebPayAdmin and associated support classes are excluded from the Svea namespace. See the generated documentation for available classes and methods.
 *
 * The underlying services and methods are contained in the Svea sub-namespaces WebService, HostedService and AdminService, and may be accessed, though their api and interfaces are subject to change in the future.
 *
 * ### Documentation format
 * See the provided README.md file for an overview and examples how to utilise the Svea\WebPay\WebPay and Svea\WebPay\WebPayAdmin classes. The complete Svea\WebPay\WebPay Integration package, including the underlying Svea service classes, methods and structures, is documented by generated documentation in the apidoc folder.
 *
 * ### Fluid API
 * The Svea\WebPay\WebPay and Svea\WebPay\WebPayAdmin entrypoint methods are built as a fluent API so you can use method chaining when implementing it in your code. We recommend making sure that your IDE code completion is enabled to make full use of this feature.
 *
 * ### Development environment
 * The Svea Svea\WebPay\WebPay PHP integration package is developed and tested using NetBeans IDE 7.3.1 with the phpunit 3.7.24 plugin.
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class WebPayAdmin
{

    /**
     * The Svea\WebPay\WebPayAdmin::cancelOrder() entrypoint method is used to cancel an order with Svea,
     * that has not yet been delivered (invoice, payment plan) or confirmed (card).
     *
     * Supports Invoice, Payment Plan and Card orders. For Direct Bank orders, use Svea\WebPay\WebPayAdmin::creditOrderRows() instead.
     *
     * Get an instance using the Svea\WebPay\WebPayAdmin::cancelOrder entrypoint, then provide more information about the order and send
     * the request using the CancelOrderBuilder methods:
     *
     * ...
     *     $request = Svea\WebPay\WebPayAdmin->cancelOrder($config)
     *          ->setOrderId()        // required, use SveaOrderId recieved with createOrder response
     *          ->setTransactionId()    // optional, card or direct bank only, alias for setOrderId
     *          ->setCountryCode()    // required, use same country code as in createOrder request
     *     ;
     *     // then select the corresponding request class and send request
     *     $response = $request->cancelInvoiceOrder()->doRequest();    // returns CloseOrderResponse
     *     $response = $request->cancelPaymentPlanOrder()->doRequest();    // returns CloseOrderResponse
     *     $response = $request->cancelCardOrder()->doRequest();    // returns AnnulTransactionResponse
     * ...
     *
     * @see \Svea\CancelOrderBuilder \Svea\WebPay\BuildOrder\CancelOrderBuilder
     * @see \Svea\WebService\CloseOrderResult Svea\WebPay\WebService\WebServiceResponse\CloseOrderResult
     * @see \Svea\HostedService\AnnulTransactionResponse \Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\AnnulTransactionResponse
     *
     * @param \Svea\WebPay\Config\ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\CancelOrderBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function cancelOrder($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new CancelOrderBuilder($config);
    }

    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException()
    {
        throw new ValidationException('-missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider 
        object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. 
        Set returnvalues to configuration values. Create an object from that class. 
        Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). 
        You can replace the default config values into config files to return your own config values.');
    }

    /**
     * The Svea\WebPay\WebPayAdmin::queryOrder entrypoint method is used to get information about an order.
     *
     * Note that for invoice and payment plan orders, the order rows name and description is merged
     * into the description field in the query response.
     *
     * Get an instance using the Svea\WebPay\WebPayAdmin::queryOrder entrypoint, then provide more information
     * about the order and send the request using the QueryOrderBuilder methods:
     *
     * ...
     *      $request = Svea\WebPay\WebPay::queryOrder($config)
     *          ->setOrderId()          // required, use SveaOrderId recieved with createOrder response
     *          ->setTransactionId()    // optional, card or direct bank only, alias for setOrderId
     *          ->setCountryCode()      // required, use same country code as in createOrder request
     *      ;
     *      // then select the corresponding request class and send request
     *      $response = $request->queryInvoiceOrder()->doRequest();     // returns GetOrdersResponse
     *      $response = $request->queryPaymentPlanOrder()->doRequest(); // returns GetOrdersResponse
     *      $response = $request->queryCardOrder()->doRequest();        // returns QueryTransactionResponse
     *      $response = $request->queryDirectBankOrder()->doRequest();  // returns QueryTransactionResponse
     * ...
     *
     * @see \Svea\QueryOrderBuilder \Svea\WebPay\BuildOrder\QueryOrderBuilder
     * @see \Svea\AdminService\GetOrdersResponse \Svea\WebPay\AdminService\AdminServiceResponse\GetOrdersResponse
     * @see \Svea\HostedService\QueryTransactionResponse \Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse
     *
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\QueryOrderBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function queryOrder($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new QueryOrderBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::cancelOrderRows entrypoint method is used to cancel rows in an order before it has been delivered.
     * Supports Invoice, AccountCredit, Payment Plan and Card orders. (Direct Bank orders are not supported, see CreditOrderRows instead.)
     *
     * For Invoice, AccountCredit and Payment Plan orders, the order row status is updated at Svea following each successful request.
     *
     * For card orders, the request can only be sent once, and if all original order rows are cancelled, the order then receives status ANNULLED at Svea.
     *
     * Get an instance using the Svea\WebPay\WebPayAdmin::queryOrder entrypoint, then provide more information about the order and
     * send the request using the queryOrderBuilder methods:
     *
     * Use setRowToCancel() or setRowsToCancel() to specify the order row(s) to cancel. The order row indexes should correspond to those returned by
     * i.e. Svea\WebPay\WebPayAdmin::queryOrder();
     *
     * For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in a copy of the original order rows. The original order rows can
     * be retrieved using Svea\WebPay\WebPayAdmin::queryOrder(); the numberedOrderRows attribute contains the serverside order rows w/indexes. Note that if a card
     * order has been modified (i.e. rows cancelled or credited) after the initial order creation, the returned order rows will not be accurate.
     *
     *  ...
     *      $request = Svea\WebPay\WebPayAdmin::cancelOrderRows($config)
     *          ->setOrderId()                // required
     *          ->setTransactionId()            // optional, card only, alias for setOrderId
     *          ->setCountryCode()            // required
     *          ->setRowToCancel()            // required, index of original order rows you wish to cancel
     *          ->addNumberedOrderRow()            // required for card orders, should match original row indexes
     *      ;
     *      // then select the corresponding request class and send request
     *      $response = $request->deliverInvoiceOrderRows()->doRequest();       // returns CancelOrderRowsResponse
     *      $response = $request->deliverPaymentPlanOrderRows()->doRequest();   // returns CancelOrderRowsResponse
     *      $response = $request->deliverCardOrderRows()->doRequest();          // returns LowerTransactionResponse
     * ...
     *
     * @see \Svea\CancelOrderRowsBuilder \Svea\WebPay\BuildOrder\CancelOrderRowsBuilder
     * @see \Svea\AdminService\CancelOrderRowsResponse \Svea\WebPay\AdminService\AdminServiceResponse\CancelOrderRowsResponse
     * @see \Svea\HostedService\LowerTransactionResponse \Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\LowerTransactionResponse
     *
     * @param \Svea\WebPay\Config\ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\CancelOrderRowsBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function cancelOrderRows($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new CancelOrderRowsBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::creditOrderRows entrypoint method is used to credit rows in an order after it has been delivered.
     * Supports invoice, PaymentPlan, card and direct bank orders.
     * (To credit a payment plan order, please contact Svea customer service first.)
     *
     * If you wish to credit an amount not present in the original order, use addCreditOrderRow() or addCreditOrderRows()
     * and supply a new order row for the amount to credit. This is the recommended way to credit a card or direct bank order.
     *
     * If you wish to credit an invoice or Payment Plan order row in full, you can specify the index of the order row to credit using setRowToCredit().
     * The corresponding order row at Svea will then be credited. (For card or direct bank orders you need to first query and then
     * supply the corresponding numbered order rows using the addNumberedOrderRows() method.)
     *
     * Following the request Svea will issue a credit invoice including the original order rows specified using setRowToCredit(),
     * as well as any new credit order rows specified using addCreditOrderRow(). For card or direct bank orders, the order row amount
     * will be credited to the customer.
     *
     * Get an order builder instance using the Svea\WebPay\WebPayAdmin::creditOrderRows entrypoint, then provide more information about the
     * transaction and send the request using the creditOrderRowsBuilder methods:
     *
     * ...
     *     $request = Svea\WebPay\WebPay::creditOrder($config)
     *         ->setInvoiceId()                // invoice only, required
     *         ->setInvoiceDistributionType()  // invoice only, required
     *         ->setContractNumber()           // Paymentplan only, required
     *         ->setOrderId()                  // card and direct bank only, required
     *         ->setCountryCode()              // required
     *         ->addCreditOrderRow()           // optional, use to specify a new credit row, i.e. for amounts not present in the original order
     *         ->addCreditOrderRows()          // optional
     *         ->setRowToCredit()              // optional, index of one of the original order row you wish to credit
     *         ->setRowsToCredit()             // optional
     *         ->addNumberedOrderRow()         // card and direct bank only, required with setRowToCredit()
     *         ->addNumberedOrderRows()        // card and direct bank only, optional
     *     ;
     *     // then select the corresponding request class and send request
     *     $response = $request->creditInvoiceOrderRows()->doRequest();     // returns CreditInvoiceRowsResponse
     *     $response = $request->creditPaymentplanOrderRows()->doRequest(); // returns CreditPaymentPlanRowsRequest
     *     $response = $request->creditCardOrderRows()->doRequest();        // returns CreditTransactionResponse
     *     $response = $request->creditDirectBankOrderRows()->doRequest();  // returns CreditTransactionResponse
     * ...
     *
     * @param ConfigurationProvider $config
     * @return \Svea\WebPay\BuildOrder\CreditOrderRowsBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     *
     * @see    \Svea\CreditOrderRowsBuilder \Svea\WebPay\BuildOrder\CreditOrderRowsBuilder
     * @see    \Svea\AdminService\CreditInvoiceRowsResponse \Svea\WebPay\AdminService\AdminServiceResponse\CreditInvoiceRowsResponse
     * @see    \Svea\AdminService\CreditPaymentPlanResponse  \Svea\WebPay\AdminService\AdminServiceResponse\CreditPaymentPlanResponse
     * @see    \Svea\HostedService\CreditTransactionResponse \Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CreditTransactionResponse
     *
     * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
     */
    public static function creditOrderRows($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new CreditOrderRowsBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::creditAmount entrypoint method is used to credit an amount in an order after it has been delivered.
     * Supports PaymentPlan and AccountCredit
     *
     *
     * Get an order builder instance using the Svea\WebPay\WebPayAdmin::creditAmount entrypoint, then provide more information about the
     * transaction and send the request using the CreditAmountBuilder methods:
     *
     * ...
     *      $request = Svea\WebPay\WebPayAdmin::creditAmount($config)
     *              ->setContractNumber($deliverorderInfo->contractNumber)
     *              ->setCountryCode('SE')
     *              ->setDescription('credit desc')
     *              ->setAmountIncVat(100);
     *
     *      $response = $request->cancelPaymentPlanAmount()->doRequest();
     * ...
     *
     * @param \Svea\WebPay\Config\ConfigurationProvider $config
     * @return \Svea\WebPay\BuildOrder\CreditAmountBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     *
     * @author  ann-hal for Svea Ekonomi Ab | Svea\WebPay\WebPay
     */
    public static function creditAmount($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new CreditAmountBuilder($config);
    }

    /**
     * Add order rows to an order.
     *
     * Provide information about the new order rows and send the request using
     * addOrderRowsBuilder methods:
     *
     * ->setOrderId()
     * ->setCountryCode()
     * ->addOrderRow() (one or more)
     * ->addOrderRows() (optional)
     *
     * Finish by selecting the correct ordertype and perform the request:
     * ->addInvoiceOrderRows() | addPaymentPlanOrderRows()
     *   ->doRequest()
     *
     * The final doRequest() returns an AddOrderRowsResponse
     *
     * @see \Svea\AddOrderRowsBuilder \Svea\WebPay\BuildOrder\AddOrderRowsBuilder
     * @see \Svea\AdminService\AddOrderRowsResponse \Svea\WebPay\AdminService\AdminServiceResponse\AddOrderRowsResponse
     *
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\AddOrderRowsBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function addOrderRows($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new AddOrderRowsBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::updateOrderRows() method is used to update individual order rows in non-delivered invoice and
     * payment plan orders. Supports invoice and payment plan orders.
     *
     * The order row status of the order is updated at Svea to reflect the updated order rows. If the updated rows'
     * order total amount exceeds the original order total amount, an error is returned by the service.
     *
     * Get an order builder instance using the Svea\WebPay\WebPayAdmin::updateOrderRows() entrypoint, then provide more information
     * about the transaction and send the request using the UpdateOrderRowsBuilder methods:
     *
     * Use setCountryCode() to specify the country code matching the original create order request.
     *
     * Use updateOrderRow() with a new Svea\WebPay\WebPayItem::numberedOrderRow() object to pass in the updated order row. Use the
     * NumberedOrderRowBuilder member functions to specifiy the updated order row contents. Notably, the setRowNumber()
     * method specifies which original order row contents is to be replaced, in full, by the NumberedOrderRow contents.
     *
     * Then use either updateInvoiceOrderRows() or updatePaymentPlanOrderRows() to get a request object, which ever
     * matches the payment method used in the original order.
     *
     * Calling doRequest() on the request object will send the request to Svea and return UpdateOrderRowsResponse.
     *
     * ...
     *     $request = Svea\WebPay\WebPayAdmin.updateOrderRows($config)
     *         ->setOrderId()               // required
     *         ->setCountryCode()           // required
     *         ->updateOrderRow()           // required, NumberedOrderRowBuilder w/RowNumber attribute matching row index of original order row
     *     ;
     *     // then select the corresponding request class and send request
     *     $response = $request->updateInvoiceOrderRows()->doRequest();     // returns UpdateOrderRowsResponse
     *     $response = $request->updatePaymentPlanOrderRows()->doRequest(); // returns UpdateOrderRowsResponse
     * ...
     *
     * @author Kristian Grossman-Madsen
     *
     * @see    \Svea\UpdateOrderRowsBuilder \Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder
     * @see    \Svea\AdminService\UpdateOrderRowsResponse \Svea\WebPay\AdminService\AdminServiceResponse\UpdateOrderRowsResponse
     *
     * @param \Svea\WebPay\Config\ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function updateOrderRows($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new UpdateOrderRowsBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::updateOrder() method is used to add or change ClientOrderNumber and/or NotesSupports invoice
     * and payment plan orders.
     *
     * Get an order builder instance using the Svea\WebPay\WebPayAdmin::updateOrder() entrypoint, then provide more information
     * about the transaction and send the request using the UpdateOrderBuilder methods:
     *
     * Use setCountryCode() to specify the country code matching the original create order request.
     *  Use setOrderId() to specify which order
     * Use setClientOrderNumber() if you want to add or change the client order number.
     * Use setNotes() if you want to add or change the Notes on invoice from client to customer.
     * Then use either updateInvoiceOrder() or updatePaymentPlanOrder() to get a request object, which ever
     * matches the payment method used in the original order.
     *
     * Calling doRequest() on the request object will send the request to Svea and return UpdateOrderResponse.
     *
     * ...
     *     $request = Svea\WebPay\WebPayAdmin.updateOrder($config)
     *         ->setOrderId()               // required
     *         ->setCountryCode()           // required
     *         ->setClientOrderNumber() // optional
     *         ->setNotes()           // optional
     *     ;
     *     // then select the corresponding request class and send request
     *     $response = $request->updateInvoiceOrder()->doRequest();     // returns UpdateOrderResponse
     *     $response = $request->updatePaymentPlanOrder()->doRequest(); // returns UpdateOrderResponse
     * ...
     *
     * @see \Svea\UpdateOrderBuilder \Svea\WebPay\BuildOrder\UpdateOrderBuilder
     * @see \Svea\AdminService\UpdateOrderResponse \Svea\WebPay\AdminService\AdminServiceResponse\UpdateOrderResponse
     *
     * @param \Svea\WebPay\Config\ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\UpdateOrderBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function updateOrder($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new UpdateOrderBuilder($config);
    }

    /**
     * The Svea\WebPay\WebPayAdmin::deliverOrderRows entrypoint method is used to deliver individual order rows. Supports invoice and card orders.
     * (To partially deliver PaymentPlan or Direct Bank orders, please contact Svea.)
     *
     * For Invoice orders, the order row status is updated at Svea following each successful request.
     *
     * For card orders, an order can only be delivered once, and any non-delivered order rows will be cancelled (i.e. the order amount
     * will be lowered by the sum of the non-delivered order rows). A delivered card order has status CONFIRMED at Svea.
     *
     * Get an order builder instance using the Svea\WebPay\WebPayAdmin::deliverOrderRows() entrypoint, then provide more information about the
     * transaction and send the request using the DeliverOrderRowsBuilder methods:
     *
     * Use setRowToDeliver() or setRowsToDeliver() to specify the order row(s) to deliver. The order row indexes should correspond to
     * those returned by i.e. Svea\WebPay\WebPayAdmin::queryOrder();
     *
     * For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in a copy of the original order rows. The original
     * order rows can be retrieved using Svea\WebPay\WebPayAdmin::queryOrder(); the numberedOrderRows attribute contains the serverside order rows
     * w/indexes. Note that if a card order has been modified (i.e. rows cancelled or credited) after the initial order creation, the
     * returned order rows will not be accurate.
     *  ...
     *      $request = Svea\WebPay\WebPayAdmin::deliverOrderRows($config)
     *          ->setOrderId()                // required
     *          ->setTransactionId()            // optional, card only, alias for setOrderId
     *          ->setCountryCode()            // required
     *          ->setInvoiceDistributionType()          // required, invoice only
     *          ->setRowToDeliver()            // required, index of original order rows you wish to cancel
     *          ->addNumberedOrderRow()            // required for card orders, should match original row indexes
     *      ;
     *      // then select the corresponding request class and send request
     *      $response = $request->deliverInvoiceOrderRows()->doRequest();       // returns DeliverOrderRowsResponse
     *      $response = $request->deliverPaymentPlanOrderRows()->doRequest();   // returns DeliverOrderRowsResponse
     *      $response = $request->deliverCardOrderRows()->doRequest();          // returns ConfirmTransactionResponse
     * ...
     *
     * @see \Svea\DeliverOrderRowsBuilder \Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder
     * @see \Svea\AdminService\DeliverOrderRowsResponse \Svea\AdminService\DeliverOrderRowsResponse
     * @see \Svea\HostedService\ConfirmTransactionResponse \Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse
     *
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function deliverOrderRows($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new DeliverOrderRowsBuilder($config);
    }


    /**
     * The Svea\WebPay\WebPayAdmin::queryTaskInfo entry point method is used to retrieve information regarding desired task.
     *
     * For Invoice orders, the order row status is updated at Svea following each successful request.
     *
     *  ...
     *      $request = Svea\WebPay\WebPayAdmin::queryTaskInfo($config)
     *          ->setTaskUrl($taskUrl);
     *
     *      // then select the corresponding request class and send request
     *      $response = $request->queryTaskInfo()->doRequest();       // returns TaskInfoResponse
     * ...
     *
     * @see \Svea\DeliverOrderRowsBuilder \Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder
     *
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     * @return \Svea\WebPay\BuildOrder\QueryTaskInfoBuilder
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public static function queryTaskInfo($config = null)
    {
        if ($config == null) {
            self::throwMissingConfigException();
        }

        return new QueryTaskInfoBuilder($config);
    }

}
