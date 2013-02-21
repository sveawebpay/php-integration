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
    function testFailOnNullCustomerRefNo() {
        $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                         ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                        )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
        ->usePayPageCardOnly();
       $order->getPaymentForm();
        
    }
     
    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    function testFailOnEmptyCustomerRefNo() {
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
                    ->usePayPageCardOnly();
       $order->getPaymentForm();
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
    function testFailOnMissingCustomerForNL(){
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
                    ->usePayPage();
       $order->getPaymentForm();
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
    function testFailOnMissingCompanyCustomerForNL(){
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
                    ->usePayPage();
       $order->getPaymentForm();
     }
    
     /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : Currency is required. Use function setCurrency().
     */
    function testFailOnMissingCurrency(){
         $builder = WebPay::createOrder();
        $order = $builder
                ->addOrderRow(Item::orderRow()
                          ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                        )
                ->setCountryCode("SE")
                ->setClientOrderNumber("34")
                    ->usePayPageCardOnly();
       $order->getPaymentForm();
    }
     
     
}

?>
