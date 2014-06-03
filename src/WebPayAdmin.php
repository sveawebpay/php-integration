<?php
// WebPayAdmin class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * WebPayAdmin provides entrypoints to the various administrative functions 
 * provided by Svea.
 * 
 * @version 2.0b
 * @author Kristian Grossman-Madsen for Svea WebPay
 * @package WebPay
 * @api 
 */
class WebPayAdmin {

    /**
     * Add order rows to an order. Supports Invoice and Payment Plan orders.
     * (Card and Direct Bank orders are not supported.)
     * 
     * Provide information about the new order rows and send the request using 
     * @see addOrderRowsBuilder methods:
     * ->setOrderId()
     * ->setCountryCode()
     * ->addOrderRow() (one or more)
     * ->addOrderRows() (optional)
     *  
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\AddOrderRowsBuilder
     */
    public static function addOrderRows( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\AddOrderRowsBuilder($config);
    }               
    
    /**
     * Cancel an undelivered/unconfirmed order. Supports Invoice, PaymentPlan 
     * and Card orders. (For Direct Bank orders, see CreditOrder instead.)
     *  
     * Use the following methods to set the order attributes needed in the request: 
     * ->setOrderId(sveaOrderId or transactionId from createOrder response)
     * ->setCountryCode()
     * 
     * Then select the correct ordertype and perform the request:
     * ->cancelInvoiceOrder() | cancelPartPaymentOrder() | cancelCardOrder()
     *   ->doRequest
     * 
     * The final doRequest() response is of one of the following types and may 
     * contain different attributes depending on the original payment method:
     * @see CloseOrderResult (Invoice or PartPayment orders) or
     * @see HostedAdminResponse (Card orders)
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\CancelOrderBuilder object
     * @throws Exception
     */
    public static function cancelOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }        
        return new Svea\CancelOrderBuilder($config);
    }
    
    /**
     * Query information about an order. Supports all order payment methods.
     * 
     * Use the following methods (@see QueryOrderBuilder):
     * ->setOrderId()
     * ->setCountryCode()  
     * 
     * Then select the correct ordertype and perform the request:
     * ->queryInvoiceOrder() | queryPaymentPlanOrder() | queryCardOrder() | queryDirectBankOrder()
     *   ->doRequest()
     *  
     * The final doRequest() response is of one of the following types and may 
     * contain different attributes depending on the original payment method:
     * @see Svea\GetOrdersResponse (Invoice or PartPayment orders) or
     * @see Svea\QueryTransactionResponse (Card or DirectBank orders)
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\QueryOrderBuilder
     * @throws Exception
     */
    public static function queryOrder( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\QueryOrderBuilder($config);
    }
    
    /**
     * Cancel order rows in an order. Supports Invoice, Payment Plan and Card orders.
     * (Direct Bank orders are not supported, see CreditOrderRows instead.)
     * 
     * Use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
     * the queryOrder response numberedOrderRows attribute contains the order rows.
     * 
     * Then provide more information about the transaction and send the request using 
     * @see cancelOrderRowsBuilder methods:
     * ->setOrderId()
     * ->setCountryCode()
     * ->setRowToCancel() (one or more)
     * ->setRowsToCancel() (optional)
     * ->setNumberedOrderRows() (card only)
     *  
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\CancelOrderRowsBuilder
     * @throws ValidationException
     */
    public static function cancelOrderRows( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\CancelOrderRowsBuilder($config);
    }


// * updateOrderRows -- update order rows in non-delivered invoice or payment plan order, 
//         or lower amount to charge (only) for non-confirmed card orders
// * 	->updateInvoiceOrderRows(): use admin service UpdateOrderRows with given (numbered order row, Svea\OrderRow object) pairs to update order rows
// * 	->updatePaymentPlanOrderRows(): as for invoice
// * 	->updateCardOrderRows(): -- only possible to lower the amount for an order, method should ensure this.
// * 	->updateDirectBankRows(): -- not supported
// * 
// * 	Implement admin service UpdateOrderRows
// * 	Create AdminSoap classes
// * 	Create UpdateOrderRowsResult class
// * 	Create UpdateOrderRowsBuilder class
// * 	->updateOrderRows( Svea\NumberedOrderRow:updatedOrderRow)
// * 	->setOrderId()
// * 	Validation of OrderBuilder attributes needed to place request
// * 
// * 	Card: check if amount is <= current amount, or return error message
// * 	Card: do LowerTransaction request
// *
    
    /**
     * Update order rows in an non-delivered invoice or payment plan order, 
     * or lower amount to charge in non-confirmed card orders. Supports Invoice 
     * and Payment Plan orders, limited support for Card orders. (Direct Bank 
     * orders are not supported.)
     * 
     * Provide information about the updated order rows and send the request using 
     * @see updateOrderRowsBuilder methods:
     * ->setOrderId()
     * ->setCountryCode()
     * ->updateOrderRow() (one or more)
     * ->updateOrderRows() (optional)
     *  
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\UpdateOrderRowsBuilder
     * @throws ValidationException
     */
    public static function updateOrderRows( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\UpdateOrderRowsBuilder($config);
    }
    
    /**
     * Credit order rows in a delivered invoice order, a charged card order or 
     * a direct bank order. Supports all payment methods.
     * 
     * Add the delivered order row(s) to credit using the setRowtoCredit() or 
     * setRowsToCredit() methods. The row numbers should reflect the invoice 
     * order rows you wish to credit.
     *
     * You can also add additional credit rows to include in the credit invoice,
     * specify these using the addCreditOrderRow() or addCreditOrderRows() methods.
     * 
     * For credited invoice orders, you will also receive a reference to the new
     * credit invoice issued with the response.
     * 
     * @see updateOrderRowsBuilder methods:
     * ->setCountryCode()
     * ->setInvoiceId()
     * ->setRowToCredit() (one or more)
     * ->setRowsToCredit() (optional)
     * ->addCreditOrderRow() (optional)
     * ->addCreditOrderRows() (optional)
     *  
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\UpdateOrderRowsBuilder
     * @throws ValidationException
     */
    public static function creditOrderRows( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\CreditOrderRowsBuilder($config);
    }  
    
////////////////////////////////////////////////////////////////////////////////////////    
    
//    // HostedRequest/HandleOrder
//    
//    /**
//     * annulTransaction is used to cancel (annul) a card transaction. The 
//     * transaction must have status AUTHORIZED or CONFIRMED at Svea. (Indicating 
//     * that the transaction has not yet been captured (settled).)
//     * 
//     * Use the WebPayAdmin::annulTransaction() entrypoint to get an instance of
//     * AnnulTransaction. Then provide more information about the transaction and
//     * send the request using @see AnnulTransaction methods.
//     * 
//     * @param ConfigurationProvider $config
//     * @return \Svea\AnnulTransaction
//     */
//    static function annulTransaction($config) {
//        return new Svea\AnnulTransaction($config);
//    }
//    
//    /**
//     * confirmTransaction can be performed on card transaction having the status 
//     * AUTHORIZED. This will result in a CONFIRMED transaction that will be
//     * captured on the given capturedate.
//     * 
//     * Note that this method only supports Card transactions.
//     * 
//     * Use the WebPayAdmin::confirmTransaction() entrypoint to get an instance of
//     * ConfirmTransaction. Then provide more information about the transaction and
//     * send the request using @see ConfirmTransaction methods.
//     * 
//     * @param ConfigurationProvider $configs
//     * @return \Svea\ConfirmTransaction
//     */
//    static function confirmTransaction($config) {
//        return new Svea\ConfirmTransaction($config);
//    }
//    
//    /**
//     * creditTransaction can be used to credit transactions. Only transactions that
//     * have reached the status SUCCESS can be credited.
//     * 
//     * Use the WebPayAdmin::creditTransaction() entrypoint to get an instance of
//     * CreditTransaction. Then provide more information about the transaction and
//     * send the request using @see CreditTransaction methods.
//     * 
//     * @param ConfigurationProvider $configs
//     * @return \Svea\CreditTransaction
//     */
//    static function creditTransaction($config) {
//        return new Svea\CreditTransaction($config);
//    }    
//    
//    /**
//     * listPaymentMethods fetches all paymentmethods connected to the given 
//     * ConfigurationProvider and country.
//     *
//     * Use the WebPayAdmin::listPaymentMethods() entrypoint to get an instance of
//     * ListPaymentMethods. Then provide more information about the transaction and
//     * send the request using @see ListPaymentMethod methods. 
//     * 
//     * Following the ->doRequest call you receive a @see \Svea\ListPaymentMethodsResponse
//     * 
//     * @param ConfigurationProvider $configs
//     * @return \Svea\ListPaymentMethods
//     */
//    static function listPaymentMethods($config) {
//        return new Svea\ListPaymentMethods($config);
//    }  
//
//    /**
//     * lowerTransaction modifies the amount in an existing card transaction 
//     * having status AUTHORIZED or CONFIRMED. If the amount is lowered by an 
//     * amount equal to the transaction authorized amount, then after a 
//     * successful request the transaction will get the status ANNULLED.
//     * 
//     * Use the WebPayAdmin::lowerTransaction() entrypoint to get an instance of
//     * LowerTransaction. Then provide more information about the transaction and
//     * send the request using @see LowerTransaction methods.
//     * 
//     * Following the ->doRequest call you receive a @see \Svea\LowerTransactionResponse
//     * 
//     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
//     * @return Svea\LowerTransaction
//     * @throws Exception
//     * 
//     */
//    public static function lowerTransaction( $config = NULL ) {
//        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
//        
//        return new Svea\LowerTransaction($config);
//    }
//    
//    /**
//     * Query information about an existing card or direct bank transaction.
//     * 
//     * Use the WebPayAdmin::queryTransaction() entrypoint to get an instance of
//     * QueryTransaction. Then provide more information about the transaction and
//     * send the request using @see QueryTransaction methods.
//     * 
//     * Note that this only supports queries based on the Svea transactionId.
//     *
//     * @param ConfigurationProvider $config  instance of implementation class of ConfigurationProvider Interface
//     * @return Svea\QueryTransaction
//     * @throws Exception
//     */
//    public static function queryTransaction( $config = NULL ) {
//        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
//        return new Svea\QueryTransaction($config);
//    }
      
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
