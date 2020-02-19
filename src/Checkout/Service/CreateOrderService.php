<?php

namespace Svea\WebPay\Checkout\Service;

use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\CreateOrderValidator;

/**
 * Class CreateOrderService is responsible for preparing, validating and
 * passing data for creating an order to checkout connection library
 *
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service
 */
class CreateOrderService extends CheckoutService
{
    public $requestObject;

    /**
     * Send call to Connection Library
     *
     * @return array
     */
    public function doRequest()
    {
        $requestData = $this->prepareRequest();

        $response = $this->serviceConnection->create($requestData);

        return $response;
    }

    /**
     * Process Order for request data for Svea Checkout API
     *
     * @return array
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    protected function prepareRequest()
    {
        $errors = $this->validateOrder();
        $this->processErrors($errors);
        $data = $this->mapCreateOrderData($this->order);

        return $data;
    }

    /**
     * Validate order data
     *
     * @return array of errors
     */
    protected function validateOrder()
    {
        $validator = new CreateOrderValidator();
        $errors = $validator->validate($this->order);

        return $errors;
    }

    /**
     * Map Order to array
     *
     * @param CheckoutOrderBuilder $order
     * @return array
     */
    protected function mapCreateOrderData(CheckoutOrderBuilder $order)
    {
        $data = array();

        /**
         * @var \Svea\WebPay\WebService\SveaSoap\SveaOrderRow [] $orderItems
         */
        $orderItems = $this->formatOrderInformationWithOrderRows();

        foreach ($orderItems as $item) {
            $data['cart']['items'][] = $this->mapOrderItem($item);
        }

        $data['currency'] = $order->currency;
        $data['countryCode'] = $order->countryCode;
        $data['locale'] = $order->getLocale();

        $data['merchantSettings'] = $order->getMerchantSettings()->getMerchantSettings();

        $data['clientOrderNumber'] = $order->getClientOrderNumber();

        if (count($order->getPresetValues()) > 0) {
            foreach ($order->getPresetValues() as $presetValue) {
                $data['presetValues'] [] = $presetValue->returnPresetArray();
            }
        }

        if ($order->getPartnerKey() != null)
        {
            $data['partnerKey'] = $order->getPartnerKey();
        }

        if ($order->getIdentityFlags() != null)
        {
            foreach ($order->getIdentityFlags() as $key => $identityFlag)
            {
                $data['identityFlags'][$identityFlag] = true;
            }
        }

        $data['merchantData'] = $order->getMerchantData();

        if($order->getRequireElectronicIdAuthentication() != null)
        {
            $data['requireElectronicIdAuthentication'] = $order->getRequireElectronicIdAuthentication();
        }

        return $data;
    }
}
