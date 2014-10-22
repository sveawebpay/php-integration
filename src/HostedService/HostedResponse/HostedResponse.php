<?php
namespace Svea\HostedService;
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author anne-hal, Kristian Grossman-Madsen for Svea WebPay
 */
class HostedResponse {

    /** @var int $accepted  Set to 1 iff transaction was accepted by Svea. A value of 0 may indicate that the request failed, see $resultcode. */
    public $accepted;
    /** @var string $resultcode  A value >0 indicates that the service request failed at Svea, see $errormessage. 0 indicates a malformed message.  */
    public $resultcode;    
    /** @var string $errormessage  Human readable explanation of the service resultcode. */
    public $errormessage;
  
    protected function setErrorParams($resultcode) {
        switch ($resultcode) {

        case '1':
            $this->resultcode = $resultcode. ' (REQUIRES_MANUAL_REVIEW)';
            $this->errormessage = 'Request performed successfully but requires manual review from merchant. Applicable paymentmethods: PAYPAL.';
            break;
        
        case '100':
            $this->resultcode = $resultcode. ' (INTERNAL_ERROR)';
            $this->errormessage = 'Invalid – contact integrator.';
            break;
        case '101':
            $this->resultcode = $resultcode. ' (XMLPARSEFAIL)';
            $this->errormessage = 'Invalid XML.';
            break;
        case '102':
            $this->resultcode = $resultcode. ' (ILLEGAL_ENCODING)';
            $this->errormessage = 'Invalid encoding.';
            break;
        case '104':
            $this->resultcode = $resultcode. ' (ILLEGAL_URL)';
            $this->errormessage = 'Illegal Url.';
            break;
        case '105':
            $this->resultcode = $resultcode. ' (ILLEGAL_TRANSACTIONSTATUS)';
            $this->errormessage = 'Invalid transaction status.';
            break;
        case '106':
            $this->resultcode = $resultcode. ' (EXTERNAL_ERROR)';
            $this->errormessage = 'Failure at third party e.g. failure at the bank.';
            break;
        case '107':
            $this->resultcode = $resultcode. ' (DENIED_BY_BANK)';
            $this->errormessage = 'Transaction rejected by bank.';
            break;
        case '108':
            $this->resultcode = $resultcode. ' (CANCELLED)';
            $this->errormessage = 'Transaction cancelled.';
            break;
        case '109':
            $this->resultcode = $resultcode. ' (NOT_FOUND_AT_BANK)';
            $this->errormessage = 'Transaction not found at the bank.';
            break;
        case '110':
            $this->resultcode = $resultcode. ' (ILLEGAL_TRANSACTIONID)';
            $this->errormessage = 'Invalid transaction ID.';
            break;
        case '111':
            $this->resultcode = $resultcode. ' (MERCHANT_NOT_CONFIGURED)';
            $this->errormessage = 'Merchant not configured.';
            break;
        case '112':
            $this->resultcode = $resultcode. ' (MERCHANT_NOT_CONFIGURED_AT_BANK)';
            $this->errormessage = 'Merchant not configured at the bank.';
            break;
        case '113':
            $this->resultcode = $resultcode. ' (PAYMENTMETHOD_NOT_CONFIGURED)';
            $this->errormessage = 'Payment method not configured for merchant.';
            break;
        case '114':
            $this->resultcode = $resultcode. ' (TIMEOUT_AT_BANK)';
            $this->errormessage = 'Timeout at the bank.';
            break;
        case '115':
            $this->resultcode = $resultcode. ' (MERCHANT_NOT_ACTIVE)';
            $this->errormessage = 'The merchant is disabled.';
            break;
        case '116':
            $this->resultcode = $resultcode. ' (PAYMENTMETHOD_NOT_ACTIVE)';
            $this->errormessage = 'The payment method is disabled.';
            break;
        case '117':
            $this->resultcode = $resultcode. ' (ILLEGAL_AUTHORIZED_AMOUNT)';
            $this->errormessage = 'Invalid authorized amount.';
            break;
        case '118':
            $this->resultcode = $resultcode. ' (ILLEGAL_CAPTURED_AMOUNT)';
            $this->errormessage = 'Invalid captured amount.';
            break;
        case '119':
            $this->resultcode = $resultcode. ' (ILLEGAL_CREDITED_AMOUNT)';
            $this->errormessage = 'Invalid credited amount.';
            break;
        case '120':
            $this->resultcode = $resultcode. ' (NOT_SUFFICIENT_FUNDS)';
            $this->errormessage = 'Not enough founds.';
            break;
        case '121':
            $this->resultcode = $resultcode. ' (EXPIRED_CARD)';
            $this->errormessage = 'The card has expired.';
            break;
        case '122':
            $this->resultcode = $resultcode. ' (STOLEN_CARD)';
            $this->errormessage = 'Stolen card.';
            break;
        case '123':
            $this->resultcode = $resultcode. ' (LOST_CARD)';
            $this->errormessage = 'Lost card.';
            break;
        case '124':
            $this->resultcode = $resultcode. ' (EXCEEDS_AMOUNT_LIMIT)';
            $this->errormessage = 'Amount exceeds the limit.';
            break;
        case '125':
            $this->resultcode = $resultcode. ' (EXCEEDS_FREQUENCY_LIMIT)';
            $this->errormessage = 'Frequency limit exceeded.';
            break;
        case '126':
            $this->resultcode = $resultcode. ' (TRANSACTION_NOT_BELONGING_TO_MERCHANT)';
            $this->errormessage = 'Transaction does not belong to merchant).';
            break;
        case '127':
            $this->resultcode = $resultcode. ' (CUSTOMERREFNO_ALREADY_USED)';
            $this->errormessage = 'Customer reference number already used in another transaction.';
            break;
        case '128':
            $this->resultcode = $resultcode. ' (NO_SUCH_TRANS)';
            $this->errormessage = 'Transaction does not exist.';
            break;
        case '129':
            $this->resultcode = $resultcode. ' (DUPLICATE_TRANSACTION)';
            $this->errormessage = 'More than one transaction found for the given customer reference number.';
            break;
        case '130':
            $this->resultcode = $resultcode. ' (ILLEGAL_OPERATION)';
            $this->errormessage = 'Operation not allowed for the given payment method.';
            break;
        case '131':
            $this->resultcode = $resultcode. ' (COMPANY_NOT_ACTIVE)';
            $this->errormessage = 'Company inactive.';
            break;
        case '132':
            $this->resultcode = $resultcode. ' (SUBSCRIPTION_NOT_FOUND)';
            $this->errormessage = 'No subscription exist.';
            break;
        case '133':
            $this->resultcode = $resultcode. ' (SUBSCRIPTION_NOT_ACTIVE)';
            $this->errormessage = 'Subscription not active.';
            break;
        case '134':
            $this->resultcode = $resultcode. ' (SUBSCRIPTION_NOT_SUPPORTED)';
            $this->errormessage = 'Payment method doesn’t support subscriptions.';
            break;
        case '135':
            $this->resultcode = $resultcode. ' (ILLEGAL_DATE_FORMAT)';
            $this->errormessage = 'Illegal date format.';
            break;
        case '136':
            $this->resultcode = $resultcode. ' (ILLEGAL_RESPONSE_DATA)';
            $this->errormessage = 'Illegal response data.';
            break;
        case '137':
            $this->resultcode = $resultcode. ' (IGNORE_CALLBACK)';
            $this->errormessage = 'Ignore callback.';
            break;
        case '138':
            $this->resultcode = $resultcode. ' (CURRENCY_NOT_CONFIGURED)';
            $this->errormessage = 'Currency not configured.';
            break;
        case '139':
            $this->resultcode = $resultcode. ' (CURRENCY_NOT_ACTIVE)';
            $this->errormessage = 'Currency not active.';
            break;
        case '140':
            $this->resultcode = $resultcode. ' (CURRENCY_ALREADY_CONFIGURED)';
            $this->errormessage = 'Currency is already configured.';
            break;
        case '141':
            $this->resultcode = $resultcode. ' (ILLEGAL_AMOUNT_OF_RECURS_TODAY)';
            $this->errormessage = 'Ilegal amount of recurs per day.';
            break;
        case '142':
            $this->resultcode = $resultcode. ' (NO_VALID_PAYMENT_METHODS)';
            $this->errormessage = 'No valid paymentmethods.';
            break;
        case '143':
            $this->resultcode = $resultcode. ' (CREDIT_DENIED_BY_BANK)';
            $this->errormessage = 'Credit denied by bank.';
            break;
        case '144':
            $this->resultcode = $resultcode. ' (ILLEGAL_CREDIT_USER)';
            $this->errormessage = 'User is not allowed to perform credit operation.';
            break;

        case '300':
            $this->resultcode = $resultcode. ' (BAD_CARDHOLDER_NAME)';
            $this->errormessage = 'Invalid value for cardholder name.';
            break;
        case '301':
            $this->resultcode = $resultcode. ' (BAD_TRANSACTION_ID)';
            $this->errormessage = 'Invalid value for transaction id.';
            break;
        case '302':
            $this->resultcode = $resultcode. ' (BAD_REV)';
            $this->errormessage = 'Invalid value for rev.';
            break;
        case '303':
            $this->resultcode = $resultcode. ' (BAD_MERCHANT_ID)';
            $this->errormessage = 'Invalid value for merchant id.';
            break;
        case '304':
            $this->resultcode = $resultcode. ' (BAD_LANG)';
            $this->errormessage = 'Invalid value for lang.';
            break;
        case '305':
            $this->resultcode = $resultcode. ' (BAD_AMOUNT)';
            $this->errormessage = 'Invalid value for amount.';
            break;
        case '306':
            $this->resultcode = $resultcode. ' (BAD_CUSTOMERREFNO)';
            $this->errormessage = 'Invalid value for customer refno 307.';
            break;
        case '307':
            $this->resultcode = $resultcode. ' (BAD_CURRENCY)';
            $this->errormessage = 'Invalid value for currency.';
            break;
        case '308':
            $this->resultcode = $resultcode. ' (BAD_PAYMENTMETHOD)';
            $this->errormessage = 'Invalid value for payment method.';
            break;
        case '309':
            $this->resultcode = $resultcode. ' (BAD_RETURNURL)';
            $this->errormessage = 'Invalid value for return url.';
            break;
        case '310':
            $this->resultcode = $resultcode. ' (BAD_LASTBOOKINGDAY)';
            $this->errormessage = 'Invalid value for last booking day.';
            break;
        case '311':
            $this->resultcode = $resultcode. ' (BAD_MAC)';
            $this->errormessage = 'Invalid value for mac.';
            break;
        case '312':
            $this->resultcode = $resultcode. ' (BAD_TRNUMBER)';
            $this->errormessage = 'Invalid value for tr number.';
            break;
        case '313':
            $this->resultcode = $resultcode. ' (BAD_AUTHCODE)';
            $this->errormessage = 'Invalid value for authcode.';
            break;
        case '314':
            $this->resultcode = $resultcode. ' (BAD_CC_DESCR)';
            $this->errormessage = 'Invalid value for cc_descr.';
            break;
        case '315':
            $this->resultcode = $resultcode. ' (BAD_ERROR_CODE)';
            $this->errormessage = 'Invalid value for error_code.';
            break;
        case '316':
            $this->resultcode = $resultcode. ' (BAD_CARDNUMBER_OR_CARDTYPE_NOT_CONFIGURED)';
            $this->errormessage = 'Card type not configured for merchant.';
            break;
        case '317':
            $this->resultcode = $resultcode. ' (BAD_SSN)';
            $this->errormessage = 'Invalid value for ssn.';
            break;
        case '318':
            $this->resultcode = $resultcode. ' (BAD_VAT)';
            $this->errormessage = 'Invalid value for vat.';
            break;
        case '319':
            $this->resultcode = $resultcode. ' (BAD_CAPTURE_DATE)';
            $this->errormessage = 'Invalid value for capture date.';
            break;
        case '320':
            $this->resultcode = $resultcode. ' (BAD_CAMPAIGN_CODE_INVALID)';
            $this->errormessage = 'Invalid value for campaign code. There are no valid matching campaign codes.';
            break;
        case '321':
            $this->resultcode = $resultcode. ' (BAD_SUBSCRIPTION_TYPE)';
            $this->errormessage = 'Invalid subscription type.';
            break;
        case '322':
            $this->resultcode = $resultcode. ' (BAD_SUBSCRIPTION_ID)';
            $this->errormessage = 'Invalid subscription id.';
            break;
        case '323':
            $this->resultcode = $resultcode. ' (BAD_BASE64)';
            $this->errormessage = 'Invalid base64.';
            break;
        case '324':
            $this->resultcode = $resultcode. ' (BAD_CAMPAIGN_CODE)';
            $this->errormessage = 'Invalid campaign code. Missing value.';
            break;
        case '325':
            $this->resultcode = $resultcode. ' (BAD_CALLBACKURL)';
            $this->errormessage = 'Invalid callbackurl.';
            break;
        case '326':
            $this->resultcode = $resultcode. ' (THREE_D_CHECK_FAILED)';
            $this->errormessage = '3D check failed.';
            break;
        case '327':
            $this->resultcode = $resultcode. ' (CARD_NOT_ENROLLED)';
            $this->errormessage = 'Card not enrolled in 3D secure.';
            break;
        case '328':
            $this->resultcode = $resultcode. ' (BAD_IPADDRESS)';
            $this->errormessage = 'Provided ip address is incorrect.';
            break;
        case '329':
            $this->resultcode = $resultcode. ' (BAD_MOBILE)';
            $this->errormessage = 'Bad mobile phone number.';
            break;
        case '330':
            $this->resultcode = $resultcode. ' (BAD_COUNTRY)';
            $this->errormessage = 'Bad country parameter.';
            break;
        case '331':
            $this->resultcode = $resultcode. ' (THREE_D_CHECK_NOT_AVAILABLE)';
            $this->errormessage = 'Merchants 3D configuration invalid.';
            break;
        case '332':
            $this->resultcode = $resultcode. ' (TIMEOUT)';
            $this->errormessage = 'Timeout at Svea.';
            break;
        
        case '500':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_CARDBIN_NOT_ALLOWED)';
            $this->errormessage = 'Antifraud - cardbin not allowed.';
            break;
        case '501':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_IPLOCATION_NOT_ALLOWED)';
            $this->errormessage = 'Antifraud – iplocation not allowed.';
            break;
        case '502':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_IPLOCATION_AND_BIN_DOESNT_MATCH)';
            $this->errormessage = 'Antifraud – ip-location and bin does not match.';
            break;
        case '503':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_MAX_AMOUNT_PER_IP_EXCEEDED)';
            $this->errormessage = 'Antofraud – max amount per ip exceeded.';
            break;
        case '504':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_MAX_TRANSACTIONS_PER_IP_EXCEEDED)';
            $this->errormessage = 'Antifraud – max transactions per ip exceeded.';
            break;
        case '505':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_MAX_TRANSACTIONS_PER_CARDNO_EXCEEDED)';
            $this->errormessage = 'Antifraud – max transactions per card number exceeded.';
            break;
        case '506':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_MAX_AMOUNT_PER_CARDNO_EXCEEDED)';
            $this->errormessage = 'Antifraud – max amount per cardnumer exceeded.';
            break;
        case '507':
            $this->resultcode = $resultcode. ' (ANTIFRAUD_IP_ADDRESS_BLOCKED)';
            $this->errormessage = 'Antifraud – IP address blocked.';
            break;
        
        default:
            $this->resultcode = $resultcode. ' (UNKNOWN_ERROR)';
            $this->errormessage = 'Unknown error.';
            break;
        }
    }

    /**
     * Validates that the received mac can be reconstructed from the message and
     * the shared secret.
     * 
     * @param string $messageEncoded
     * @param string $mac
     * @param string $secret
     * @return boolean  true iff the mac can be validated
     */
    public function validateMac($messageEncoded,$mac,$secret) {

        $macKey = hash("sha512", $messageEncoded.$secret);

        if ($mac == $macKey) {
            return TRUE;
        }
        return FALSE;
    }
}
