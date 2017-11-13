<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CheckoutAdminOrderBuilder;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Helper\CheckoutRowFormatter;
use Svea\WebPay\Checkout\Service\Connection\CheckoutAdminConnection;

/**
 * Class AdminImplementationService
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service\Adnin
 */
abstract class AdminImplementationService
{
    /**
     * @var CheckoutAdminConnection $checkoutAdminConnection
     */
    protected $checkoutAdminConnection;

    /**
     * @var CheckoutAdminOrderBuilder $adminBuilder
     */
    protected $adminBuilder;

    /**
     * CheckoutService constructor.
     *
     * @param CheckoutAdminOrderBuilder $adminBuilder
     */
    public function __construct(CheckoutAdminOrderBuilder $adminBuilder)
    {
        $this->adminBuilder = $adminBuilder;
        $this->checkoutAdminConnection = new CheckoutAdminConnection($adminBuilder->conf, $adminBuilder->countryCode);
    }

    /**
     * Validate order data
     */
    abstract public function validate();

    /**
     * Format given date so that will match data structure required for Admin API
     * @return mixed
     */
    abstract public function prepareRequest();

    /**
     * Send call Connection Library
     */
    abstract public function doRequest();

    /**
     * @param array $errors
     * @throws ValidationException
     */
    protected function processErrors(array $errors)
    {
        if (count($errors) > 0) {
            $message = '';
            foreach ($errors as $key => $val) {
                $message = "Error - $key : $val";
                break;
            }

            throw new ValidationException($message);
        }
    }

    /**
     * @return mixed
     */
    protected function formatOrderInformationWithOrderRows()
    {
        $formatter = new CheckoutRowFormatter($this);
        $formattedOrderRows = $formatter->formatRows();

        return $formattedOrderRows;
    }
}
