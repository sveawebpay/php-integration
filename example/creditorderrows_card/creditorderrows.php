<?php
/**
 * example file, how to credit an existing card order
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
error_reporting( E_ALL );
ini_set('display_errors', 'On');

// Include Svea PHP integration package.
$svea_directory = "../../src/";
require_once( $svea_directory . "Includes.php" );

// get config object
$myConfig = Svea\SveaConfig::getTestConfig(); //replace with class holding your merchantid, secretword, et al, adopted from package Config/SveaConfig.php

// We wish to credit the entire card order. To do so, we need to credit the order with an amount equal to the original order total.
// This can be done by either :
// 1) specifying a new credit row for the entire original order total amount and passing in the new row to credit, or
// 2) specifying that all of the original order rows are to be credited, passing in their number and the order rows themselves

// 1) Using the first method:

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
 
// Assume that we know that the original order total amount was 1*(100*1.25) + 2*(5.00*1.12) = 125+11.2 = SEK 136.2 (incl. VAT 26.2)
// Create a new OrderRow for the credited amount and add it to the builder object using addCreditOrderRow():
$myCreditRow =  WebPayItem::orderRow()
                ->setAmountExVat( 110.00 )
                ->setAmountIncVat( 136.20 )
                ->setQuantity( 1 )
                ->setDescription( "Credited in full")
;

// Add the new order row to credit to the builder object.
$firstCreditOrderRowsBuilder->addCreditOrderRow($myCreditRow);

// Then we can send the credit request to Svea:

$myCreditRequest = $firstCreditOrderRowsBuilder->creditCardOrderRows();        
$myCreditResponse = $myCreditRequest->doRequest();

/*

// 2) instead credit specific order rows, 2):
$secondCreditOrderRowsBuilder = WebPayAdmin::creditOrderRows( $myConfig );

$secondCreditOrderRowsBuilder
    ->setOrderId( $myTransactionId )
    ->setCountryCode( "SE" )
;

// To credit specific order rows, you pass the order rows numbers you wish to credit using setRowsToCredit().
// For card orders, you also need to pass in the numbered order rows themselves using addNumberedOrderRows(). 

// You can use the WebPayAdmin::queryCardOrder() entrypoint method to get a copy of the original order rows sent to Svea.
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

*/

// The response is an instance of LowerTransactionResponse 
echo "<pre>";
print_r( "the creditCardOrderRows() response:");
print_r( $myCreditResponse );

echo "\n</pre><font color='red'><pre>\n\n
An example of a non-successful credit request response, where the card order had not yet been processed (i.e. Svea transactionstatus doesn't equal SUCCESS).

the creditCardOrderRows() response:Svea\HostedService\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T13:49:48 02:00
    [accepted] => 0
    [resultcode] => 105 (ILLEGAL_TRANSACTIONSTATUS)
    [errormessage] => Invalid transaction status.
)

An example of a non-successful credit request response, where the card order has already been credited for the full amount.

the creditCardOrderRows() response:Svea\HostedService\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T14:28:33 02:00
    [accepted] => 0
    [resultcode] => 119 (ILLEGAL_CREDITED_AMOUNT)
    [errormessage] => Invalid credited amount.
)";

echo "\n</pre><font color='blue'><pre>\n\n
An example of a non-successful credit request response, where the card order has been first confirmed and then processed to status SUCCESS in bank.

the creditCardOrderRows() response:Svea\HostedService\CreditTransactionResponse Object
(
    [customerrefno] => order #2014-08-26T14:28:33 02:00
    [accepted] => 1
    [resultcode] => 0
    [errormessage] => 
)

The following is the raw result of a queryOrder for the above order, as you can see the entire authorized/captured amount has been credited:

</pre><font color='black'><xmp>
<?xml version=\"1.0\" encoding=\"UTF-8\"?><response>
  <transaction id=\"585602\">
    <customerrefno>order #2014-08-26T14:28:33 02:00</customerrefno>
    <merchantid>1130</merchantid>
    <status>SUCCESS</status>
    <amount>13620</amount>
    <currency>SEK</currency>
    <vat>2620</vat>
    <capturedamount>13620</capturedamount>
    <authorizedamount>13620</authorizedamount>
    <created>2014-08-26 14:28:34.937</created>
    <creditstatus>CREDSUCCESS</creditstatus>
    <creditedamount>13620</creditedamount>
    <merchantresponsecode>0</merchantresponsecode>
    <paymentmethod>KORTCERT</paymentmethod>
    <callbackurl/>
    <capturedate>2014-08-26 14:35:10.807</capturedate>
    <subscriptionid/>
    <subscriptiontype/>
    <customer id=\"12171\">
      <firstname/>
      <lastname/>
      <initials/>
      <email/>
      <ssn/>
      <address/>
      <address2/>
      <city/>
      <country>SE</country>
      <zip/>
      <phone/>
      <vatnumber/>
      <housenumber/>
      <companyname/>
      <fullname/>
    </customer>
    <orderrows>
      <row>
        <id>53037</id>
        <name/>
        <amount>12500</amount>
        <vat>2500</vat>
        <description>Ivar</description>
        <quantity>1.0</quantity>
        <sku/>
        <unit/>
      </row>
      <row>
        <id>53038</id>
        <name/>
        <amount>560</amount>
        <vat>60</vat>
        <description>Korv med brÃ¶d</description>
        <quantity>2.0</quantity>
        <sku/>
        <unit/>
      </row>
    </orderrows>
  </transaction>
  <statuscode>0</statuscode>
</response>
";

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
