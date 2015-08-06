<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Class used by HostedPayment
 * Contains:
 * Complete form without submit in html format: $completeHtmlFormWithSubmitButton
 * Array of formfields in html format: $htmlFormFieldsAsArray
 * Values for form: $merchantId, $xmlMessageBase64, $mac
 * Array of formfields in raw format: $xmlMessage
 * Raw fields: $xmlMessageBase64, $message, $merchantId, $secretWord, $mac
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentForm {

    public $endPointUrl;
    public $xmlMessage;
    public $xmlMessageBase64;
    public $merchantid;
    public $secretWord;
    public $mac;
    public $completeHtmlFormWithSubmitButton;
    public $htmlFormFieldsAsArray = array();
    private $submitMessage;
    private $noScriptMessage;

    /**
     * populates the payment form object from the given parameters and generates
     * the $completeHtmlFormWithSubmitButton & $htmlFormFieldsAsArray attributes
     *
     * @param type $xmlMessage
     * @param ConfigurationProvider $config
     * @param string $countryCode
     */
    function __construct( $xmlMessage, $config, $countryCode = NULL  ) {
        $this->xmlMessage = $xmlMessage;
        $this->xmlMessageBase64 = base64_encode($xmlMessage);
        $this->endPointUrl = $config->getEndPoint(\ConfigurationProvider::HOSTED_TYPE);
        $this->merchantid = $config->getMerchantId(\ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $this->secretWord = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $this->mac = hash("sha512", $this->xmlMessageBase64 . $this->secretWord);

        $this->setForm();
        $this->setHtmlFields();
        $this->setRawFields();

        $this->setSubmitMessage();
    }

    public function setRawFields() {
        $this->rawFields['merchantid'] = $this->merchantid;
        $this->rawFields['message'] = $this->xmlMessageBase64;
        $this->rawFields['mac'] = $this->mac;
        $this->rawFields['htmlFormMethod'] = 'post';
        $this->rawFields['htmlFormAction'] = $this->endPointUrl;
    }

    public function setSubmitMessage($countryCode = FALSE) {
        switch ($countryCode) {
            case "SE":
                $this->submitMessage = "Betala";
                $this->noScriptMessage = "Javascript är inaktiverat i er webbläsare, så ni får manuellt omdirigera till paypage";
                break;
            default:
                $this->submitMessage = "Submit";
                $this->noScriptMessage = "Javascript is inactivated in your browser, you will manually have to redirect to the paypage";
                break;
        }
    }

    /**
     * Set complete html-form as string
     */
    public function setForm() {
        $formString = "<form name='paymentForm' id='paymentForm' method='post' action='".$this->endPointUrl."'>";
        $formString .= "<input type='hidden' name='merchantid' value='{$this->merchantid}' />";
        $formString .= "<input type='hidden' name='message' value='{$this->xmlMessageBase64}' />";
        $formString .= "<input type='hidden' name='mac' value='{$this->mac}' />";
        $formString .= "<noscript><p>".$this->noScriptMessage."</p></noscript>";
        $formString .= "<input type='submit' name='submit' value='".$this->submitMessage."' />";
        $formString .= "</form>";

        $this->completeHtmlFormWithSubmitButton = $formString;
    }

    /**
     * Set form elements as Array
     */
    public function setHtmlFields() {
        $this->htmlFormFieldsAsArray['form_start_tag'] = "<form name='paymentForm' id='paymentForm' method='post' action='".$this->endPointUrl."'>";
        $this->htmlFormFieldsAsArray['input_merchantId'] = "<input type='hidden' name='merchantid' value='{$this->merchantid}' />";
        $this->htmlFormFieldsAsArray['input_message'] = "<input type='hidden' name='message' value='{$this->xmlMessageBase64}' />";
        $this->htmlFormFieldsAsArray['input_mac'] = "<input type='hidden' name='mac' value='{$this->mac}' />";
        $this->htmlFormFieldsAsArray['noscript_p_tag'] = "<noscript><p>".$this->noScriptMessage."</p></noscript>";
        $this->htmlFormFieldsAsArray['input_submit'] = "<input type='submit' name='submit' value='".$this->submitMessage."' />";
        $this->htmlFormFieldsAsArray['form_end_tag'] = "</form>";
    }
}
