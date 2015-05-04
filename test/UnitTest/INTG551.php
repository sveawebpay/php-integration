<?php
$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class INTG551UnitTest extends \PHPUnit_Framework_TestCase {
    
    
    
    
    /// characterizing tests for INTG-551
    function test_creditOrderRows_handles_creditOrderRows_specified_using_exvat_and_vatpercent() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountExVat(10.00)
                        ->setVatPercent(25)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();
     
        $this->assertEquals("10", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
        $this->assertEquals(null, $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);    
        
        
        
        
        
        
    }
    function test_creditOrderRows_handles_creditOrderRows_specified_using_incvat_and_vatpercent() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountIncVat(10.00)
                        ->setVatPercent(25)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();
     
        $this->assertEquals("10", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
        $this->assertEquals(true, $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);    
    }    
    function test_creditOrderRows_handles_creditOrderRows_specified_using_incvat_and_exvat() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountIncVat(12.50)
                        ->setAmountExVat(10.00)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();

    $this->assertEquals("12.50", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
    $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
    $this->assertEquals("1", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);    
    }
}
