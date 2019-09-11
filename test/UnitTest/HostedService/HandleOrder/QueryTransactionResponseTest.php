<?php


namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;


use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse;

class QueryTransactionResponseTest extends \PHPUnit\Framework\TestCase
{
    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_negative_row()
    {
        $test = QueryTransactionResponse::calculateVatPercentFromVatAndAmount("-1252", "-6262");

        $this->assertEquals("25.00", $test);
    }

    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_positive_row()
    {
        $test = QueryTransactionResponse::calculateVatPercentFromVatAndAmount("1252", "6262");

        $this->assertEquals("25.00", $test);
    }

    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_zero_amount_row()
    {
        $test = QueryTransactionResponse::calculateVatPercentFromVatAndAmount("0", "0");

        $this->assertEquals("0.00", $test);
    }
}