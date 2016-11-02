<?php
/**
 * example file, how to credit an existing card order using the queried original order rows 
 * 
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebPayAdmin;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

// get config object
$myConfig = ConfigurationService::getTestConfig(); // add your Svea credentials into config_prod.php or config_test.php file

// We wish to credit the entire card order. To do so, we need to credit the order with an amount equal to the original order total.
// This can be done by either :
// 1) (recommended) specifying a new credit row for the entire original order total amount and passing in the new row to credit, or
// 2) specifying that all of the original order rows are to be credited, passing in their number and, as this is a card order, the order rows themselves

// 2) instead credit specific order rows, 2):
// 
// First we create a builder object and populate them with the required fields.
$firstCreditOrderRowsBuilder = WebPayAdmin::creditOrderRows( $myConfig );

// To credit the order, we need its transactionid, which we received with the order request response and wrote to file.
$myTransactionId = file_get_contents("transactionid.txt");
if( ! $myTransactionId ) {
    echo "<pre>Error: transactionid.txt not found, first run cardorder_credit.php to set up the card order. aborting.";
    die;    
}
$firstCreditOrderRowsBuilder
    ->setOrderId( $myTransactionId )
    ->setCountryCode( "SE" )
;

$secondCreditOrderRowsBuilder = WebPayAdmin::creditOrderRows( $myConfig );

$secondCreditOrderRowsBuilder
    ->setOrderId( $myTransactionId )
    ->setCountryCode( "SE" )
;

// To credit specific order rows, you pass the order rows numbers you wish to credit using setRowsToCredit().
// For card orders, you also need to pass in the numbered order rows themselves using addNumberedOrderRows(). 

// You can use the Svea\WebPay\WebPayAdmin::queryCardOrder() entrypoint method to get a copy of the original order rows sent to Svea.
// Note that these order rows does not update following a successful credit order rows request, even though the
// QueryTransactionResponse field creditedamount returned by a queryOrder request will reflect the current credit status.  
//
$queryOrderBuilder = WebPayAdmin::queryOrder( $myConfig )
    ->setOrderId( $myTransactionId )
    ->setCountryCode( "SE" )
;

// query orderrows to pass in creditOrderRows->setNumberedOrderRows()
$queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest(); 
if( ! $queryResponse->accepted ) {
    echo "<pre>Error: queryOrder failed. aborting.";
    die;    
}

// The query response holds an array of NumberedOrderRow containing the order rows as sent in the createOrder request
$myOriginalOrderRows = $queryResponse->numberedOrderRows;

// Put the numbered order row indexes into an array to pass to setRowsToCredit()
$myRowIndexesToCredit = range( 1, count($myOriginalOrderRows) ); // original order rows are 1-indexed and contains no gaps

// Add the indexes to credit and the order row data to the builder object.
$secondCreditOrderRowsBuilder
    ->addNumberedOrderRows( $myOriginalOrderRows )
    ->setRowsToCredit( $myRowIndexesToCredit )
;

// Send the credit request to Svea:
$myCreditRequest = $secondCreditOrderRowsBuilder->creditCardOrderRows();        
$myCreditResponse = $myCreditRequest->doRequest();


// The response is an instance of LowerTransactionResponse 
echo "<pre>";
print_r( "the creditCardOrderRows() response:");
print_r( $myCreditResponse );

echo "\n</pre><font color='red'><pre>\n\n
An example of a non-successful credit request response, where the card order had not yet been processed (i.e. Svea transactionstatus doesn't equal SUCCESS).

the creditCardOrderRows() response:Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T13:49:48 02:00
    [accepted] => 0
    [resultcode] => 105 (ILLEGAL_TRANSACTIONSTATUS)
    [errormessage] => Invalid transaction status.
)

An example of a non-successful credit request response, where the card order has already been credited for the full amount.

the creditCardOrderRows() response:Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T14:28:33 02:00
    [accepted] => 0
    [resultcode] => 119 (ILLEGAL_CREDITED_AMOUNT)
    [errormessage] => Invalid credited amount.
)";

echo "\n</pre><font color='blue'><pre>\n\n
An example of a non-successful credit request response, where the card order has been first confirmed and then processed to status SUCCESS in bank.

the creditCardOrderRows() response:Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T14:28:33 02:00
    [accepted] => 1
    [resultcode] => 0
    [errormessage] => 
)

The following is the result of a Svea\WebPay\WebPayAdmin::queryOrder for the above order, as you can see the entire authorized/captured amount has been credited:

</pre>
<pre><font color='black'>
Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse Object
(
    [transactionId] => 589756
    [clientOrderNumber] => order #2014-11-20T16:00:34 01:00
    [merchantId] => 1130
    [status] => SUCCESS
    [amount] => 37500
    [currency] => SEK
    [vat] => 7500
    [capturedamount] => 37500
    [authorizedamount] => 37500
    [created] => 2014-11-20 16:00:35.737
    <font color='blue'>[creditstatus] => CREDSUCCESS
    [creditedamount] => 37500<font color='black'>
    [merchantresponsecode] => 0
    [paymentMethod] => KORTCERT
    [numberedOrderRows] => Array
        (
            [0] => Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow Object
                (
                    [creditInvoiceId] => 
                    [invoiceId] => 
                    [rowNumber] => 1
                    [status] => 
                    [articleNumber] => 
                    [quantity] => 1
                    [unit] => 
                    [amountExVat] => 100
                    [vatPercent] => 25
                    [amountIncVat] => 
                    [name] => 
                    [description] => A
                    [discountPercent] => 
                    [vatDiscount] => 0
                )

            [1] => Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow Object
                (
                    [creditInvoiceId] => 
                    [invoiceId] => 
                    [rowNumber] => 2
                    [status] => 
                    [articleNumber] => 
                    [quantity] => 1
                    [unit] => 
                    [amountExVat] => 100
                    [vatPercent] => 25
                    [amountIncVat] => 
                    [name] => 
                    [description] => B
                    [discountPercent] => 
                    [vatDiscount] => 0
                )

            [2] => Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow Object
                (
                    [creditInvoiceId] => 
                    [invoiceId] => 
                    [rowNumber] => 3
                    [status] => 
                    [articleNumber] => 
                    [quantity] => 1
                    [unit] => 
                    [amountExVat] => 100
                    [vatPercent] => 25
                    [amountIncVat] => 
                    [name] => 
                    [description] => C
                    [discountPercent] => 
                    [vatDiscount] => 0
                )

        )

    [callbackurl] => 
    [capturedate] => 2014-11-20 16:02:20.26
    [subscriptionId] => 
    [subscriptiontype] => 
    [cardType] => 
    [maskedCardNumber] => 
    [eci] => 
    [mdstatus] => 
    [expiryYear] => 
    [expiryMonth] => 
    [chname] => 
    [authCode] => 
    [accepted] => 1
    [resultcode] => 0
    [errormessage] => 
)
</pre>";

/**
 * get the path to this file, for use in specifying the returnurl etc.
 */
function getPath() {
    $myURL = $_SERVER['SCRIPT_NAME'];
    $myPath = explode('/', $myURL);
    unset( $myPath[count($myPath)-1]);
    $myPath = implode( '/', $myPath);

    return $myPath;
}
?>
