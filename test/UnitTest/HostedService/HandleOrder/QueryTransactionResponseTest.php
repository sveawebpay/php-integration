<?php


namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;


use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse;
use Svea\WebPay\Response\SveaResponse;

class QueryTransactionResponseTest extends \PHPUnit\Framework\TestCase
{
    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_negative_row()
    {
        $queryTransactionResponse = new QueryTransactionResponse("", "", "");

        $test = $queryTransactionResponse->calculateVatPercentFromVatAndAmount("-1252", "-6262");

        $this->assertEquals("25.00", $test);
    }

    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_positive_row()
    {
        $queryTransactionResponse = new QueryTransactionResponse("", "", "");

        $test = $queryTransactionResponse->calculateVatPercentFromVatAndAmount("1252", "6262");

        $this->assertEquals("25.00", $test);
    }

    function test_calculateVatPercentFromVatAndAmount_returns_correct_vatpercent_if_transactions_contains_zero_amount_row()
    {
        $queryTransactionResponse = new QueryTransactionResponse("", "", "");

        $test = $queryTransactionResponse->calculateVatPercentFromVatAndAmount("0", "0");

        $this->assertEquals("0.00", $test);
    }

    function test_QueryTransactionResponse_contains_amountincvat_and_amountexvat()
    {
        $message = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><response><message>PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48cmVzcG9uc2U+PHRyYW5zYWN0aW9uIGlkPSI3MTU2NDgiPjxjdXN0b21lcnJlZm5vPjQ5MzcyMDU8L2N1c3RvbWVycmVmbm8+PG1lcmNoYW50aWQ+MTEzMDwvbWVyY2hhbnRpZD48c3RhdHVzPlNVQ0NFU1M8L3N0YXR1cz48YW1vdW50PjU2MzYzPC9hbW91bnQ+PGN1cnJlbmN5PlNFSzwvY3VycmVuY3k+PHZhdD4xMTI3MzwvdmF0PjxjYXB0dXJlZGFtb3VudD41NjM2MzwvY2FwdHVyZWRhbW91bnQ+PGF1dGhvcml6ZWRhbW91bnQ+NTYzNjM8L2F1dGhvcml6ZWRhbW91bnQ+PGNyZWF0ZWQ+MjAxOS0wOS0wOSAxNzowOToyOS43NTc8L2NyZWF0ZWQ+PGNyZWRpdHN0YXR1cz5DUkVERkFJTDwvY3JlZGl0c3RhdHVzPjxjcmVkaXRlZGFtb3VudD41NjM2MjwvY3JlZGl0ZWRhbW91bnQ+PG1lcmNoYW50cmVzcG9uc2Vjb2RlPjA8L21lcmNoYW50cmVzcG9uc2Vjb2RlPjxwYXltZW50bWV0aG9kPlNWRUFDQVJEUEFZPC9wYXltZW50bWV0aG9kPjxjYWxsYmFja3VybD5odHRwOi8vbG9jYWxob3N0OjgwL2RldmVsb3Avb3BlbmNhcnQtMy4wLjMuMS9pbmRleC5waHA/cm91dGU9ZXh0ZW5zaW9uL3BheW1lbnQvc3ZlYV9jYXJkL2NhbGxiYWNrU3ZlYTwvY2FsbGJhY2t1cmw+PGNhcHR1cmVkYXRlPjIwMTktMDktMTAgMDA6MDU6MTkuNzQzPC9jYXB0dXJlZGF0ZT48c3Vic2NyaXB0aW9uaWQvPjxzdWJzY3JpcHRpb250eXBlLz48Y3VzdG9tZXIgaWQ9IjExODYyNiI+PGZpcnN0bmFtZS8+PGxhc3RuYW1lLz48aW5pdGlhbHMvPjxlbWFpbC8+PHNzbi8+PGFkZHJlc3MvPjxhZGRyZXNzMi8+PGNpdHkvPjxjb3VudHJ5PlNFPC9jb3VudHJ5Pjx6aXAvPjxwaG9uZS8+PHZhdG51bWJlci8+PGhvdXNlbnVtYmVyLz48Y29tcGFueW5hbWUvPjxmdWxsbmFtZS8+PGluZHVzdHJ5Y29kZS8+PGlzY29tcGFueT5mYWxzZTwvaXNjb21wYW55Pjx1bmtub3duY3VzdG9tZXI+ZmFsc2U8L3Vua25vd25jdXN0b21lcj48L2N1c3RvbWVyPjxtYXNrZWRjYXJkbm8+NDkxNjQyKioqKioqODEwMjwvbWFza2VkY2FyZG5vPjxjYXJkdHlwZT5WSVNBPC9jYXJkdHlwZT48ZWNpLz48bWRzdGF0dXMvPjxleHBpcnl5ZWFyPjIwPC9leHBpcnl5ZWFyPjxleHBpcnltb250aD41PC9leHBpcnltb250aD48Y2huYW1lPi08L2NobmFtZT48YXV0aGNvZGU+MTEzMDQ0PC9hdXRoY29kZT48b3JkZXJyb3dzPjxyb3c+PGlkPjI1NTUwMDwvaWQ+PG5hbWU+aVBob25lPC9uYW1lPjxhbW91bnQ+NjI2MjU8L2Ftb3VudD48dmF0PjEyNTI1PC92YXQ+PGRlc2NyaXB0aW9uPjwvZGVzY3JpcHRpb24+PHF1YW50aXR5PjEuMDwvcXVhbnRpdHk+PHNrdT5wcm9kdWN0IDExPC9za3U+PHVuaXQvPjwvcm93Pjxyb3c+PGlkPjI1NTUwMTwvaWQ+PG5hbWU+RnJlZSBTaGlwcGluZzwvbmFtZT48YW1vdW50PjA8L2Ftb3VudD48dmF0PjA8L3ZhdD48ZGVzY3JpcHRpb24+PC9kZXNjcmlwdGlvbj48cXVhbnRpdHk+MS4wPC9xdWFudGl0eT48c2t1PnNoaXBwaW5nPC9za3U+PHVuaXQvPjwvcm93Pjxyb3c+PGlkPjI1NTUwMjwvaWQ+PG5hbWU+Q291cG9uICgyMjIyKSBNb21za2xhc3M6MjUlPC9uYW1lPjxhbW91bnQ+LTYyNjI8L2Ftb3VudD48dmF0Pi0xMjUyPC92YXQ+PGRlc2NyaXB0aW9uPjwvZGVzY3JpcHRpb24+PHF1YW50aXR5PjEuMDwvcXVhbnRpdHk+PHNrdT48L3NrdT48dW5pdC8+PC9yb3c+PC9vcmRlcnJvd3M+PC90cmFuc2FjdGlvbj48c3RhdHVzY29kZT4wPC9zdGF0dXNjb2RlPjwvcmVzcG9uc2U+</message><merchantid>1130</merchantid><mac>c973795d51add05a20f82fe030a34866edf0ce2abfbb41b829e0f161f53291f95cc74fdb50e1d86e2ab0d2eee8a53cd3f3418ff6266fc6af987b83fbfaccfd82</mac></response>");
        $countryCode = "SE";
        $config = ConfigurationService::getTestConfig();

        $queryTransactionResponse = new QueryTransactionResponse($message, $countryCode, $config);

        $this->assertEquals("501.0",$queryTransactionResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("626.25",$queryTransactionResponse->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals("0",$queryTransactionResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals("0",$queryTransactionResponse->numberedOrderRows[1]->amountIncVat);
        $this->assertEquals("-50.1",$queryTransactionResponse->numberedOrderRows[2]->amountExVat);
        $this->assertEquals("-62.62",$queryTransactionResponse->numberedOrderRows[2]->amountIncVat);

    }
}