<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcludePayments
 *
 * @author anne-hal
 */
class ExcludePayments {
    
    public function excludeInvoicesAndPaymentPlan($countryCode) {
        $methods = array();
        
                $methods[] = PaymentMethod::SVEAINVOICESE;
                $methods[] = PaymentMethod::SVEASPLITSE;
                $methods[] = PaymentMethod::SVEAINVOICEEU_SE;
                $methods[] = PaymentMethod::SVEASPLITEU_SE;
          
                $methods[] = PaymentMethod::SVEAINVOICEEU_DE;
                $methods[] = PaymentMethod::SVEASPLITEU_DE;
           
                $methods[] = PaymentMethod::SVEAINVOICEEU_DK;
                $methods[] = PaymentMethod::SVEASPLITEU_DK;
             
                $methods[] = PaymentMethod::SVEAINVOICEEU_FI;
                $methods[] = PaymentMethod::SVEASPLITEU_FI;
            
                $methods[] = PaymentMethod::SVEAINVOICEEU_NL;
                $methods[] = PaymentMethod::SVEASPLITEU_NL;
          
                $methods[] = PaymentMethod::SVEAINVOICEEU_NO;
                $methods[] = PaymentMethod::SVEASPLITEU_NO;
            
        
        return $methods;
    }
}

?>
