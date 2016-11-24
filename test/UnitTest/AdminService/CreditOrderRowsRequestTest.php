<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreditOrderRowsRequestTest extends \PHPUnit_Framework_TestCase
{

    /// characterising test for INTG-462
    // invoice
    public function test_creditOrderRows_creditInvoiceOrderRows_does_not_validate_setOrderId()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId(987654)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode('SE')
            ->setRowToCredit(1);

        // shouldn't raise any exception

        $request = $creditOrderRowsBuilder->creditInvoiceOrderRows()->prepareRequest();
    }

    // card
    public function test_creditOrderRows_creditCardOrderRows_validates_setOrderId()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            //->setOrderId(987654)    // i.e. setTransactionId()
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode('SE')
            ->setRowToCredit(1);

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', 'orderId is required for creditCardOrderRows(). Use method setOrderId().'
        );

        $request = $creditOrderRowsBuilder->creditCardOrderRows()->prepareRequest();
    }

    // direct bank
    public function test_creditOrderRows_creditDirectBankOrderRows_validates_setOrderId()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            //->setTransactionId(987654)    // alias for setOrderId()
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode('SE')
            ->setRowToCredit(1);

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', 'orderId is required for creditCardOrderRows(). Use method setOrderId().'
        );

        $request = $creditOrderRowsBuilder->creditDirectBankOrderRows()->prepareRequest();
    }

    public function test_creditOrderRows_creditPaymentPlanOrderRows_credit_row_using_row_index()
    {
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::creditOrderRows($config)
            ->setContractNumber('123123')
            ->setCountryCode('SE')
            ->setRowToCredit(1)
            ->creditPaymentPlanOrderRows()->prepareRequest();
        $this->assertEquals(1, $request->CancellationRows->enc_value[0]->enc_value->RowNumber->enc_value);
        $this->assertEquals('123123', $request->ContractNumber->enc_value);
    }

    public function test_creditOrderRows_creditPyamentplanOrderRows()
    {
        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat(123.9876)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setDescription("row 1");
        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat(10.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setDescription("row 2");
        $request = WebPayAdmin::creditOrderRows($config)
            ->setContractNumber('123132')
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditPaymentplanOrderRows()->prepareRequest();

        $this->assertEquals(123.9876, $request->CancellationRows->enc_value[0]->enc_value->AmountInclVat->enc_value);
        $this->assertEquals('123132', $request->ContractNumber->enc_value);


    }

    public function test_creditOrderRows_creditCardOrderRowsAsIncvatAndVatPercent()
    {
        $amount_inc_vat = 350;
        $vat_percent = 6;
        $quantity = 2;

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat($amount_inc_vat)
            ->setVatPercent($vat_percent)
            ->setQuantity($quantity)
            ->setDescription("row 1");

        $request = WebPayAdmin::creditOrderRows($config)
            ->setTransactionId(987654)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditCardOrderRows();

        $expected_amount = Helper::bround($amount_inc_vat * $quantity) * 100;
        $this->assertEquals($expected_amount, $request->creditAmount);
        $this->assertEquals('987654', $request->transactionId);
    }

    public function test_creditOrderRows_creditCardOrderRowsAsAmountExVatAndVatPercent()
    {
        $amount_ex_vat = 330.19;
        $vat_percent = 6;
        $quantity = 1;

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountExVat($amount_ex_vat)
            ->setVatPercent($vat_percent)
            ->setQuantity($quantity)
            ->setDescription("row 1");

        $request = WebPayAdmin::creditOrderRows($config)
            ->setTransactionId(987654)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditCardOrderRows();

        $expected_amount = Helper::bround($amount_ex_vat * (1 + $vat_percent / 100) * $quantity) * 100;
        $this->assertEquals($expected_amount, $request->creditAmount);
        $this->assertEquals('987654', $request->transactionId);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage Order with amountExVat must have vatPercent
     */
    public function test_creditOrderRowsCreditCardOrderRowsAsAmountExVatAndWithoutVatPercent()
    {
        $amount_ex_vat = 330.19;
        $quantity = 1;

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountExVat($amount_ex_vat)
            ->setQuantity($quantity)
            ->setDescription("row 1");

        WebPayAdmin::creditOrderRows($config)
            ->setTransactionId(987654)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditCardOrderRows();
    }

    public function test_creditOrderRows_creditCardOrderRowsAsAmountExVatAndAmountIncVat()
    {
        $amount_inc_vat = 350;
        $amount_ex_vat = 330.19;
        $quantity = 2;

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat($amount_inc_vat)
            ->setAmountExVat($amount_ex_vat)
            ->setVatPercent(25)
            ->setQuantity($quantity)
            ->setDescription("row 1");

        $request = WebPayAdmin::creditOrderRows($config)
            ->setTransactionId(987654)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditCardOrderRows();

        $expected_amount = Helper::bround($amount_inc_vat) * $quantity * 100;
        $this->assertEquals($expected_amount, $request->creditAmount);
        $this->assertEquals('987654', $request->transactionId);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage amountExVat or amountIncVat must be set
     */
    public function test_creditOrderRowsWithoutAmount()
    {

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setQuantity(1)
            ->setDescription("row 1");

        WebPayAdmin::creditOrderRows($config)
            ->setTransactionId(987654)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditCardOrderRows()
            ->prepareRequest();
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : Description is required.
     */
    public function test_creditOrderRows_creditPyamentplanOrderRows_noDesciription()
    {

        $config = ConfigurationService::getDefaultConfig();

        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat(10.00)
            ->setVatPercent(25)
            ->setQuantity(1);
        $orderRows[] = WebPayItem::orderRow()
            ->setAmountIncVat(10.00)
            ->setVatPercent(25)
            ->setQuantity(1);
        $credit = WebPayAdmin::creditOrderRows($config)
            ->setContractNumber(123123)
            ->setCountryCode('SE')
            ->addCreditOrderRows($orderRows)
            ->creditPaymentplanOrderRows()->prepareRequest();

    }


}
