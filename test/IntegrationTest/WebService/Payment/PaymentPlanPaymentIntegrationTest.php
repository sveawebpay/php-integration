<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\WebService\Payment;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use \PHPUnit\Framework\TestCase;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanPaymentIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function testPaymentPlanRequestReturnsAcceptedResult()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(194605092222)
                ->setInitials("SB")
                ->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setClientOrderNumber("nr26")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

}
