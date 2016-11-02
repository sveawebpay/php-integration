<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\Helper;

use Svea\WebPay\Constant\SystemPaymentMethod;
use Svea\WebPay\HostedService\Helper\ExcludePayments as ExcludePayments;


/**
 * @author Kristian Grossman-Madsen
 */
class ExcludePaymentsTest extends \PHPUnit_Framework_TestCase
{

    public function test_excludeInvoicesAndPaymentPlan_SE()
    {

        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("SE");

        $this->assertEquals(14, count((array)$excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICESE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLANSE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_SE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_SE, $excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));

        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
}