<?php


namespace Svea\WebPay\Test\IntegrationTest\HostedService\Payment;


use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;

class SwishPaymentIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateOrderWithSwish()
    {

        $clientOrderNumber = "test_swish_". rand(100000,300000);

        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber($clientOrderNumber)
            ->setCurrency("SEK")
            ->setPayerAlias("46701234567")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("https://eeeeeeeeeeeeeeeeeeeeeeeeeeeeeee.se")
            ->getPaymentForm();

        $url = "https://webpaypaymentgatewaystage.svea.com/webpay/payment";


        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key . '=' . $value . '&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // follow redirects
        curl_setopt($ch, CURLOPT_HEADER, true);             // include headers in transfer history
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // return transfer history
        $cr = curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        $swishOrder = WebpayAdmin::queryOrder($config)
            ->setCountryCode("SE")
            ->setClientOrderNumber($clientOrderNumber)
            ->queryCardOrder()
            ->doRequest();

        $this->assertEquals("SWISH", $swishOrder->paymentMethod);
        $this->assertEquals("VALID", $swishOrder->status);
    }
}