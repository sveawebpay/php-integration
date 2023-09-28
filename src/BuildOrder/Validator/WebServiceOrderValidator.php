<?php

namespace Svea\WebPay\BuildOrder\Validator;

use Svea\WebPay\Config\ConfigurationProvider;

/**
 * @author Anneli Halld'n, Daniel Brolund, Fredrik Sundell for Svea Webpay
 */
class WebServiceOrderValidator extends OrderValidator
{
    public $errors = array();

    protected $isCompany;

    /**
     * WebServiceOrderValidator constructor.
     */
    function __construct()
    {
        $this->isCompany = FALSE;
    }

    /**
     * validate($order) ensures that attributes in $order are of the right type
     * and format before creating the xmlMessage or soap calls
     *
     * @param instance of CreateOrderBuilder $order
     * @return array
     */
    public function validate($order)
    {
        if (isset($order->orgNumber) || isset($order->companyVatNumber) || isset($order->companyName)) {
            $this->isCompany = TRUE;
        }

        if (!isset($order->customerIdentity)) {
            $this->errors['missing customerIdentity'] = "customerIdentity is required. Use function addCustomerDetails().";
        }

        if (isset($order->customerIdentity->orgNumber) ||
            isset($order->customerIdentity->companyVatNumber) ||
            isset($order->customerIdentity->companyName)
        ) {

            $this->isCompany = TRUE;
        }

        $identityValidator = new IdentityValidator($this->isCompany);

        if ($order->orderType == ConfigurationProvider::PAYMENTPLAN_TYPE && $this->isCompany == TRUE) {
            $this->errors["Wrong customer type"] = "PaymentPlanPayment not allowed for Company customer.";
        }

        // Require the order to contain identication urls for swedish customers using invoice payment
        if (
            $this->isCompany === false &&
            in_array(strtoupper($order->orderType), ['INVOICE', 'PAYMENTPLAN']) &&
            strtoupper($order->countryCode) === 'SE'
        ) {
            if (!$this->validUrl($order->identificationConfirmationUrl)) {
                $this->errors['Confirmation URL not valid'] = "The confirmation url is not set or is not valid.";
            }
            if (!$this->validUrl($order->identificationRejectionUrl)) {
                $this->errors['Rejection URL not valid'] = "The rejection url is not set or is not valid.";
            }
        }

        if (isset($order->countryCode)) {
            if ($order->countryCode == "SE"
                || $order->countryCode == "NO"
                || $order->countryCode == "DK"
                || $order->countryCode == "FI"
            ) {
                $this->errors = $identityValidator->validateNordicIdentity($order, $this->errors);
            } elseif ($order->countryCode == "NL") {
                $this->errors = $identityValidator->validateNLidentity($order, $this->errors);
            } elseif ($order->countryCode == "DE") {
                $this->errors = $identityValidator->validateDEidentity($order, $this->errors);
            } else {
                $this->errors['not valid'] = "Given countrycode does not exist in our system.";
            }
        } else {
            $this->errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }

        $this->errors = $identityValidator->validateDoubleIdentity($order, $this->errors);
        $this->errors = $this->validatePeppolId($order, $this->errors);
        $this->errors = $this->validateRequiredFieldsForOrder($order, $this->errors);
        $this->errors = $this->validateOrderRows($order, $this->errors);

        if (isset($order->orderDate) == false) {
            $this->errors["missing value"] = "OrderDate is Required. Use function setOrderDate().";
        }

        return $this->errors;
    }
}
