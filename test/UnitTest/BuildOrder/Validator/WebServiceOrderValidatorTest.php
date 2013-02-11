<?php

$root = realpath(dirname(__FILE__));
require_once $root . '\..\..\..\..\src\Includes.php';

/**
 * Description of WebServiceOrderValidatorTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceOrderValidatorTest extends PHPUnit_Framework_TestCase {
    
     /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : Customer values are required for Invoice and PaymentPlan orders.
    
    function te_stFailOnMissingCustomerIdentity() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                ->setCountryCode("SE")
                    ->useInvoicePayment();
        $order->preparePayment();

       
    }
      * 
      */
    
     /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -duplicated value : Customer is either an individual or a company. You can not use function setCustomerSsn() in combination with setCustomerCompanyIdNumber() or setCustomerCompanyVatNumber().
     */
    function testFailOnDoubleIdentity() {
        $builder = WebPay::createOrder();
        $order = $builder 
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                ->setCountryCode("SE")                
                ->setCustomerSsn(194605092222)
                ->setCustomerCompanyIdNumber(4608142222)
                    ->useInvoicePayment();
       $order->preparePayment();
       
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -not valid : Given countrycode does not exist in our system.
     * 
     */
    function testFailOnBadCountryCode() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                 ->setCountryCode("ZZ")
                ->setCustomerSsn(111111)
                    ->useInvoicePayment();
        
     $order->preparePayment();
    }
    
     

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    function testFailOnMissingCountryCode() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                ->setCustomerSsn(111111)
                    ->useInvoicePayment();
        
        $order->preparePayment();
    }
    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : CustomerSsn is required for individual customers when countrycode is SE, NO, DK or FI. Use function setCustomerSsn().
     */
    function testFailOnMissingSsnForSeOrder() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                ->setCustomerName("Tess", "Testson")
                    ->useInvoicePayment();
        
       $order->preparePayment();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : OrgNumber is required for company customers when countrycode is SE, NO, DK or FI. Use function setCustomerCompanyIdNumber().
     */
    function testFailOnMissingOrgNumberForCompanyOrderSe() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                ->setCustomerReference("1")
                ->setCustomerCompanyName("Mycompany")
                    ->useInvoicePayment();
        
        $order->preparePayment();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage 
     * -missing value : CustomerBirthDate is required for individual customers when countrycode is DE. Use function setCustomerBirthDate().
     * -missing value : CustomerName is required for individual customers when countrycode is DE. Use function setCustomerName().
     * -missing value : CustomerStreetAddress is required for all customers when countrycode is DE. Use function setCustomerStreetAddress().
     * -missing value : CustomerLocality is required for all customers when countrycode is DE. Use function setCustomerLocality().
     * -missing value : CustomerZipCode is required for all customers when countrycode is DE. Use function setCustomerZipCode().
     */
    function testFailOnMissingIdentityValuesForDEPaymentPlanOrder() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("DE")
                    ->usePaymentPlanPayment(213060);

       $order->preparePayment();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : CustomerBirthDate is required for individual customers when countrycode is DE. Use function setCustomerBirthDate().
     */
    function testFailOnMissingBirthDateForDeOrder() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("DE")
                //->setCustomerBirthDate(1923, 12, 12)
                ->setCustomerName("Tess", "Testson")
                ->setCustomerStreetAddress("Gatan", 23)
                ->setCustomerZipCode(9999)
                ->setCustomerLocality("Stan")
                    ->useInvoicePayment();
        $order->preparePayment();

    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage 
     * -missing value : CustomerInitials is required for individual customers when countrycode is NL. Use function setCustomerInitials().
     * -missing value : CustomerBirthDate is required for individual customers when countrycode is NL. Use function setCustomerBirthDate().
     * -missing value : CustomerName is required for individual customers when countrycode is NL. Use function setCustomerName().
     * -missing value : CustomerStreetAddress is required for all customers when countrycode is NL. Use function setCustomerStreetAddress().
     * -missing value : CustomerLocality is required for all customers when countrycode is NL. Use function setCustomerLocality().
     * -missing value : CustomerZipCode is required for all customers when countrycode is NL. Use function setCustomerZipCode().
     */
    function testFailOnMissingValuesForNlOrder() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                ->setCountryCode("NL")
                    ->useInvoicePayment();
        //$errorArray = $order->validateOrder(); 
        //print_r($errorArray);
        $order->preparePayment(); //throws esception
        
      
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : CustomerInitials is required for individual customers when countrycode is NL. Use function setCustomerInitials().
     */
    function testFailOnMissingInitialsForNlOrder() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("NL")
                //->setCustomerInitials("SB")
                ->setCustomerBirthDate(1923, 12, 12)
                ->setCustomerName("Tess", "Testson")
                ->setCustomerStreetAddress("Gatan", 23)
                ->setCustomerZipCode(9999)
                ->setCustomerLocality("Stan")
                    ->useInvoicePayment();

      $order->preparePayment();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing values : OrderRows are required. Use function beginOrderRow() to get orderrow setters. End with endOrderRow().
     */
    function testFailOnMissingOrderRows() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                ->setCustomerReference("1")
                ->setCustomerSsn(46111111)
                    ->useInvoicePayment();
       $order->preparePayment();
      
    }
   

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage 
     * -missing values : At least two of the values must be set:  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent() after using function beginOrderRow().
     * -missing value : Quantity is required. Use function setQuantity().
     */
    function testFailOnMissingOrderRowValues() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    //->setAmountExVat(100)
                ->endOrderRow()
                ->setCountryCode("SE")
                ->setCustomerReference("ref1")
                ->setCustomerSsn(46111111)
                    ->useInvoicePayment();
        $order->preparePayment(); 
    }
}

?>
