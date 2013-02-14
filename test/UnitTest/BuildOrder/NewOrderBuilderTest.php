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
    
    function testNewInvoiceOrderWithOrderRow(){       
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
            $sveaRequest = $request->setCustomerSsn(194605092222)
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object 
                    ->prepareRequest();
            
            $this->assertEquals(194605092222, $sveaRequest->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
            $this->assertEquals(2, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
            $this->assertEquals(100.00, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
            $this->assertEquals("Prod: Specification", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
            $this->assertEquals("st", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
            $this->assertEquals(25, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
            $this->assertEquals(0, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);

        }
    
        
        function testNewInvoiceOrderWithArray(){
     
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
        
        function testOrderWithShippingFee(){
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
                    )
                ->addShippingFee(Item::shippingFee()
                        ->setShippingId(1)
                        ->setName('shipping')
                        ->setDescription("Specification")
                        ->setAmountExVat(50)
                        ->setUnit("st")
                        ->setVatPercent(25)
                        ->setDiscountPercent(0)
                        );
        //end foreach
            $sveaRequest = $request  
            ->setCustomerSsn(194605092222)
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object 
                ->prepareRequest();
            
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
            $this->assertEquals(50.00, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
            $this->assertEquals("shipping: Specification", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
            $this->assertEquals("st", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
            $this->assertEquals(25, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
            $this->assertEquals(0, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        }
        
    function testOrderWithInvoiceFee(){     
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
                    )
                ->addInvoiceFee(Item::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        );
        //end foreach
            $sveaRequest = $request  
            ->setCustomerSsn(194605092222)
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object 
                ->prepareRequest();
            
            $this->assertEquals("", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
            $this->assertEquals(50.00, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
            $this->assertEquals("Svea fee: Fee for invoice", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
            $this->assertEquals("st", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
            $this->assertEquals(25, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
            $this->assertEquals(0, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        }
    function testOrderWithFixedDiscount(){     
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
                    )
                ->addFixedDiscount(Item::fixedDiscount()
                   ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                        );
        //end foreach
            $sveaRequest = $request  
            ->setCustomerSsn(194605092222)
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object 
                ->prepareRequest();
            
            $this->assertEquals("1", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
            $this->assertEquals(-80.00, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
            $this->assertEquals("Fixed: FixedDiscount", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
            $this->assertEquals("st", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
            $this->assertEquals(25, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
            $this->assertEquals(0, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        }
    function testOrderWithRelativeDiscount(){     
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
                    ->setDiscountPercent(0))
                ->addRelativeDiscount(
                Item::relativeDiscount()
                        ->setDiscountId("1")
                        ->setDiscountPercent(50)
                        ->setUnit("st")
                        ->setName('Relative')
                        ->setDescription("RelativeDiscount")
                        );                    
        //end foreach
            $sveaRequest = $request  
            ->setCustomerSsn(194605092222)
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object 
                ->prepareRequest();
            
            $this->assertEquals("1", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
            $this->assertEquals(1, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
            $this->assertEquals(-50.00, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
            $this->assertEquals("Relative: RelativeDiscount", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
            $this->assertEquals("st", $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
            $this->assertEquals(25, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
            $this->assertEquals(0, $sveaRequest->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
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
