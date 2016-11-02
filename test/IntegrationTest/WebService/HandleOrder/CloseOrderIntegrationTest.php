<?php
namespace Svea\WebPay\Test\IntegrationTest\WebService\HandleOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

/**
 * @author Jonas Lith
 */
class CloseOrderIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Function to use in testfunctions
     * @return integer SveaOrderId
     */
    private function getInvoiceOrderId()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            //->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
            ->doRequest();

        return $request->sveaOrderId;
    }

    public function testCloseInvoiceOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
            ->setOrderId($orderId)
            ->setCountryCode("SE")
            ->closeInvoiceOrder()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function testCloseInvoiceOrder_missing_setOrderId_throws_ValidationException()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
//                ->setOrderId($orderId)
            ->setCountryCode("SE")
            ->closeInvoiceOrder()
            ->doRequest();
    }
}
