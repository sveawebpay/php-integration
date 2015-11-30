<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * HostedPayment and its descendants sets up the various hosted payment methods.
 *
 * Set all required attributes in the hosted payment class instance using the
 * setAttribute() methods. Instance methods can be chained together, as they
 * return the instance itself in a fluent fashion.
 *
 * Finish by using the getPaymentForm() method which returns an HTML form with
 * the POST request to Svea prepared. After the customer has completed the
 * hosted payment request, a response xml message is returned to the specified
 * return url, where it can be parsed using i.e. the SveaResponse class.
 *
 * Alternatively, you can use the getPaymentUrl() to get a response with
 * an URL that the customer can visit later to complete the payment at a later
 * time.
 *
 * For recurring payments, first send a payment request, using setSubscriptionType().
 * Use the initial request response subscriptionId attribute as input to subsequent
 * recur orders, using setSubscriptionId() and sending the recur request with doRecur().
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class HostedPayment {

    const RECURRINGCAPTURE = "RECURRINGCAPTURE";
    const ONECLICKCAPTURE = "ONECLICKCAPTURE";
    const RECURRING = "RECURRING";
    const ONECLICK = "ONECLICK";

    /** @var CreateOrderBuilder $order  holds the order information */
    public $order;

    /** @var string $xmlMessage  holds the generated message XML used in request */
    public $xmlMessage;

    /** @var string $xmlMessageBase64  holds the Base64-encoded $xmlMessage */
    public $xmlMessageBase64;

    /** @var string $returnUrl  holds the return URL used in request */
    public $returnUrl;

    /** @var string $callbackUrl  holds the callback URL used in request */
    public $callbackUrl;

    /** @var string $cancelUrl  holds the cancel URL used in request */
    public $cancelUrl;

    /** @var string $langCode  holds the language code used in request */
    public $langCode;

    /** @var string[] $request placeholder for the request parameter key/value pair array */
    public $request;

    /**
     * Creates a HostedPayment, sets default language to english
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        $this->langCode = "en";
        $this->order = $order;
        $this->request = array();
    }

    /**
     * Required - sets up a return url for the hosted payment response
     *
     * When a hosted payment transaction completes the payment service will answer
     * with a response xml message sent to the return url. This is also the return
     * url used if the user cancels at i.e. the SveaCardPay card payment page.
     *
     * @param string $returnUrlAsString
     * @return $this
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }

    /**
     * Optional - sets up a callback url for use if the transaction does not return correctly
     *
     * In case the hosted payment service is unable to return a response to the return url,
     * Svea will retry several times using the callback url as a fallback, if specified.
     *
     * This may happen if i.e. the user closes the browser before the payment service
     * redirects back to the shop, or if the transaction times out in lieu of user input.
     * In the latter case, Svea will fail the transaction after at most 30 minutes, and will
     * try to redirect to the callback url.
     *
     * @param string $callbackUrlAsString
     * @return $this
     */
    public function setCallbackUrl($callbackUrlAsString) {
        $this->callbackUrl = $callbackUrlAsString;
        return $this;
    }

    /**
     * Optional - includes a cancel button on the hosted pay page and sets a cancel url for use with the cancel button
     *
     * In case the payment method selection is cancelled by the user, Svea will redirect back to the cancel url.
     * Unless a cancel url is specified, no cancel button will be presented at the PayPage.
     *
     * @param string $cancelUrlAsString
     * @return $this
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }

    /* Optional - sets the pay page display language.
     *
     * Default pay page language is English, unless another is specified using this method.
     *
     * @param string $languageCodeAsISO639
     * @return $this
     */
    public function setPayPageLanguage($languageCodeAsISO639){
        switch ($languageCodeAsISO639) {
            case "sv":
            case "en":
            case "da":
            case "no":
            case "fi":
            case "es":
            case "nl":
            case "fr":
            case "de":
            case "it":
                $this->langCode = $languageCodeAsISO639;
                break;
            default:
                $this->langCode = "en";
                break;
        }
        return $this;
    }

    /**
     * getPaymentForm returns a form object containing a webservice payment request
     *
     * @return PaymentForm
     * @throws ValidationException
     */
    public function getPaymentForm() {
        //validate the order
        $errors = $this->validateOrder();
        $exceptionString = "";
        if (count($errors) > 0 || (isset($this->returnUrl) == FALSE && isset($this->paymentMethod) == FALSE)) {
            if (isset($this->returnUrl) == FALSE) {
             $exceptionString .="-missing value : ReturnUrl is required. Use function setReturnUrl().\n";
            }

            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new \Svea\ValidationException($exceptionString);
        }

        $xmlBuilder = new HostedXmlBuilder();
        $this->xmlMessage = $xmlBuilder->getPaymentXML($this->calculateRequestValues(),$this->order);
        $this->xmlMessageBase64 = base64_encode($this->xmlMessage);

        $formObject = new PaymentForm( $this->xmlMessage, $this->order->conf, $this->order->countryCode );
        return $formObject;
    }

    /**
     * getPaymentURL returns an URL to a prepared hosted payment, use this to
     * to get a link which the customer can use to confirm a payment at a later
     * time after having received the url via i.e. an email message.
     *
     * Use function setIpAddress() on the order customer.";
     * Use function setPayPageLanguage().";
     *
     * @return HostedPaymentResponse
     * [accepted] => 1
     * [resultcode] => 0
     * [errormessage] =>
     * [id] => //the order id
     * [created]
     * [url] => https://webpay.sveaekonomi.se/webpay/preparedpayment/xxxxx Will return test or prod url depending on where the order was created
     * [testurl] => https://test.sveaekonomi.se/webpay/preparedpayment/xxxxx Deprecated! Not valid if the order is created in prod.
     * @throws ValidationException
     */
    public function getPaymentUrl() {

        // follow the procedure set out in getPaymentForm, then
        //
        //validate the order
        $errors = $this->validateOrder();

        //additional validation for PreparedPayment request
        if( !isset( $this->order->customerIdentity->ipAddress ) ) {
            $errors['missing value'] = "ipAddress is required. Use function setIpAddress() on the order customer.";
        }
        if( !isset( $this->langCode) ) {
            $errors['missing value'] = "langCode is required. Use function setPayPageLanguage().";
        }

        $exceptionString = "";
        if (count($errors) > 0 || (isset($this->returnUrl) == FALSE && isset($this->paymentMethod) == FALSE)) {
            if (isset($this->returnUrl) == FALSE) {
             $exceptionString .="-missing value : ReturnUrl is required. Use function setReturnUrl().\n";
            }

            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new \Svea\ValidationException($exceptionString);
        }

        $xmlBuilder = new HostedXmlBuilder();
        $this->xmlMessage = $xmlBuilder->getPreparePaymentXML($this->calculateRequestValues(),$this->order);

        // curl away the request to Svea, and pick up the answer.

        // get our merchantid & secret

        // get the config, countryCode from the order object, $message from $this->xmlMessage;
        $this->config = $this->order->conf;
        $this->countryCode = $this->order->countryCode;
        $message = $this->xmlMessage;

        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);

        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);

        // encode the request elements
        $fields = array(
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );

        // below taken from HostedRequest doRequest
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint(\Svea\SveaConfigurationProvider::HOSTED_ADMIN_TYPE). "preparepayment");
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        $responseXML = curl_exec($ch);
        curl_close($ch);

        // create SveaResponse to handle response
        $responseObj = new \SimpleXMLElement($responseXML);
        $sveaResponse = new \SveaResponse($responseObj, $this->countryCode, $this->config);

        return $sveaResponse->response;
    }

    /**
     * @return string[] $errors an array containing the validation errors found
     */
    public function validateOrder() {
        $validator = new \Svea\HostedOrderValidator();
        $errors = $validator->validate($this->order);
        if (($this->order->countryCode == "NL" || $this->order->countryCode == "DE") && isset($this->paymentMethod)) {
            if( isset($this->paymentMethod) &&
                ($this->paymentMethod == \PaymentMethod::INVOICE || $this->paymentMethod == \PaymentMethod::PAYMENTPLAN)) {
                $errors = $validator->validateEuroCustomer($this->order, $errors);
            }
        }
        return $errors;
    }

    /**
     * returns a list of request attributes-value pairs
     */
    public function calculateRequestValues() {
        // format order data
        $formatter = new HostedRowFormatter();
        $this->request['rows'] = $formatter->formatRows($this->order);
        $this->request['amount'] = $formatter->formatTotalAmount($this->request['rows']);
        $this->request['totalVat'] = $formatter->formatTotalVat( $this->request['rows']);

        $this->request['clientOrderNumber'] = $this->order->clientOrderNumber; /// used by payment

        if (isset($this->order->customerIdentity->ipAddress)) {
             $this->request['ipAddress'] = $this->order->customerIdentity->ipAddress; /// used by payment (optional), preparepayment (required)
        }

        $this->request['langCode'] = $this->langCode;

        $this->request['returnUrl'] = $this->returnUrl;
        $this->request['callbackUrl'] = $this->callbackUrl;
        $this->request['cancelUrl'] = $this->cancelUrl;

        $this->request['currency'] = strtoupper(trim($this->order->currency));

        if (isset($this->subscriptionType)) {
             $this->request['subscriptionType'] = $this->subscriptionType;
        }

        if (isset($this->subscriptionId)) {
             $this->request['subscriptionId'] = $this->subscriptionId;
        }

        return $this->request;
    }

    /**
     * Optional - set subscription type for recurring payments.
     *
     * Subscription type may be one of
     * HostedPayment::RECURRINGCAPTURE | HostedPayment::ONECLICKCAPTURE (all countries) or
     * HostedPayment::RECURRING | HostedPayment::ONECLICK (Scandinavian countries only)
     *
     * The merchant should use RECURRINGCAPTURE if all the recurring payments are
     * to be scheduled by the merchant, without any action taken from the card holder.
     *
     * The merchant should use ONECLICKCAPTURE if they want the initial transaction to
     * be captured. In this case a successful initial transaction will result in the
     * CONFIRMED status, which means that the transaction will be captured at night when
     * the daily capture job is finished.
     *
     * The initial transaction status will either be AUTHORIZED (i.e. it may be charged
     * after it has been confirmed) or REGISTERED (i.e. the initial amount will be
     * reserved for a time by the bank, and then released) for RECURRING and ONECLICK.
     *
     * Use of setSubscriptionType() will set the attributes subscriptionId and subscriptionType
     * in the HostedPaymentResponse.
     *
     * @param string $subscriptionType  @see CardPayment constants
     * @return $this
     */
    public function setSubscriptionType( $subscriptionType ) {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    /**
     * Set a subscriptionId to use in a recurring payment request
     *
     * The subscriptionId should have been obtained in an earlier payment request response using
     * setSubscriptionType()
     *
     * @see setSubscriptionType() setSubscriptionType()
     *
     * @param string $subscriptionType
     * @return $this
     */
    public function setSubscriptionId( $subscriptionId ) {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }


    /**
     * Perform a recurring card payment request.
     *
     * Note that the specified row information in the order is used only to calculate the
     * recur order total amount. The order row information is not passed on in the request.
     * Neither is vat information passed to Svea, only the total order amount.
     *
     * If the original request subscription type was RECURRING or RECURRINGCAPTURE the currency
     * for the recur request must be the same as the currency in the initial transaction.
     *
     * @return RecurTransactionResponse
     */
    public function doRecur() {

        // calculate amount from order rows
        $formatter = new HostedRowFormatter();
        $this->request['rows'] = $formatter->formatRows($this->order);
        $this->request['amount'] = $formatter->formatTotalAmount($this->request['rows']);
        $this->request['totalVat'] = $formatter->formatTotalVat( $this->request['rows']);

        $request = new RecurTransaction( $this->order->conf );
        $request->currency = $this->order->currency;
        $request->amount = $this->request['amount'];
        $request->customerRefNo = $this->order->clientOrderNumber;
        $request->countryCode = $this->order->countryCode;
        $request->subscriptionId = $this->subscriptionId;
        $response = $request->doRequest();

        return $response;
    }
}
