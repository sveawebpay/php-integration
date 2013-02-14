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
    
    function t_estNewInvoiceOrder(){       
        $request = WebPay::createOrder()
            ->setTestmode();
        //foreach...
        $request = $request
            ->addOrderRow(
                    Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    );
        //end foreach
            $request = $request->setCustomerSsn(194605092222)
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object 
                    ->prepareRequest();
        
            print_r($request);
            $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity

        }
        
        function te_stNewInvoiceOrderWithArray(){
     
        $orderRows[] = Item::orderrow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0);
        $orderRows[] = Item::orderrow()
                    ->setArticleNumber(2)
                    ->setQuantity(2)
                    ->setAmountExVat(110.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0);
        
        
        $request = WebPay::createOrder()
            ->setTestmode()
            ->addOrderRow($orderRows)
            ->setCustomerSsn(194605092222)
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object 
                ->prepareRequest();
        
            $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity

       
        }
/** example how to integrate with array_map
        function testOrderRowsUsingMap(){
            $orderRows[] = array_map(magentoRowToOrderRow, $magentoRows);
            
            WebPay::createOrder()->addOrderRow(array_map(magentoRowToOrderRow, $magentoRows));
            
        }
        
        function magentoRowToOrderRow($magentoRow) {
             return WebPay::orderrow()
                        ->setArticleNumber($magentoRow->productId)
                        ->setQuantity(..)
                        ->setAmountExVat(...)
                        ->setDescription(...)
                        ->setName('Prod')
                        ->setUnit("st")
                        ->setVatPercent(25)
                        ->setDiscountPercent(0);
            
        }
 * 
 */

}


?>
