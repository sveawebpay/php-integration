<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * Description of HostedOrderValidatorTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HostedOrderValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnNullCustomerRefNo() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                        ->setAmountExVat(100)
                        ->setVatPercent(20)
                        ->setQuantity(1)
                )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnEmptyCustomerRefNo() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }
    
    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage
     * -missing value : Initials is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setInitials().
     * -missing value : BirthDate is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setBirthDate().
     * -missing value : Name is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setName().
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     */
    public function testFailOnMissingCustomerForNL() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                )
                ->setCountryCode("NL")
                ->setCurrency("SEK")
                ->setClientOrderNumber("55")
                ->usePaymentMethod(PaymentMethod::INVOICE)
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }
     
    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     * -missing value : VatNumber is required for INVOICE and PAYMENTPLAN payments for company customers when countrycode is NL. Use function setVatNumber().
     * -missing value : CompanyName is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setCompanyName().
     */
    public function testFailOnMissingCompanyCustomerForNL() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                )
                ->setCountryCode("NL")
                ->setCurrency("SEK")
                ->setClientOrderNumber("55")
                ->usePaymentMethod(PaymentMethod::INVOICE)
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }
    
     /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : Currency is required. Use function setCurrency().
     */
    public function testFailOnMissingCurrency() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                        ->setAmountExVat(100)
                        ->setVatPercent(20)
                        ->setQuantity(1)
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("34")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }
    
     /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    public function testFailOnMissingCountryCode() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                        ->setAmountExVat(100)
                        ->setVatPercent(20)
                        ->setQuantity(1)
                )
                //->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("34")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");
        
        $order->getPaymentForm();
    }
    
    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : ReturnUrl is required. Use function setReturnUrl().
     */
    public function testFailOnMissingReturnUrl() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("34")
                ->usePayPage();
                // ->setReturnUrl("myurl.se")
        
        $order->getPaymentForm();
    }
}

?>
