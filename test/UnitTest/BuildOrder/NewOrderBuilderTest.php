<?php

$root = realpath(dirname(__FILE__));
require_once $root . '\..\..\..\src\Includes.php';
require_once $root . '\..\..\..\src\WebServiceRequests\svea_soap\SveaSoapConfig.php';
require_once $root . '\..\VoidValidator.php';

$root = realpath(dirname(__FILE__));
require_once $root . '\TestRowFactory.php';

/**
 * All functions named test...() will run as tests in PHP-unit framework
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class NewOrderBuilderTest extends PHPUnit_Framework_TestCase {
    
    function testNewInvoiceOrder(){
        /**
        $orderRows[] = WebPay::orderrow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0);
         * 
         */
        
        $request = WebPay::createOrder()
            ->setTestmode();
        //foreach...
        $request = $request
            ->addOrderRow(
                    WebPay::orderrow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    );
            $request = $request->setCustomerSsn(194605092222)
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object 
                    ->preparePayment();
        
            print_r($request);
        }
}

?>
