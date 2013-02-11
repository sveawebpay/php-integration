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
        
        switch ($countryCode) {
            case "SE":
                $methods[] = PaymentMethod::SVEAINVOICESE;
                $methods[] = PaymentMethod::SVEASPLITSE;
                $methods[] = PaymentMethod::SVEAINVOICEEU_SE;
                $methods[] = PaymentMethod::SVEASPLITEU_SE;
                break;
            case "DE":
                $methods[] = PaymentMethod::SVEAINVOICEEU_DE;
                $methods[] = PaymentMethod::SVEASPLITEU_DE;
                break;
            case "DK":
                $methods[] = PaymentMethod::SVEAINVOICEEU_DK;
                $methods[] = PaymentMethod::SVEASPLITEU_DK;
                break;
            case "FI":
                $methods[] = PaymentMethod::SVEAINVOICEEU_FI;
                $methods[] = PaymentMethod::SVEASPLITEU_FI;
                break;
            case "NL":
                $methods[] = PaymentMethod::SVEAINVOICEEU_NL;
                $methods[] = PaymentMethod::SVEASPLITEU_NL;
                break;
            case "NO":
                $methods[] = PaymentMethod::SVEAINVOICEEU_NO;
                $methods[] = PaymentMethod::SVEASPLITEU_NO;
                break;
            default:
                break;
        }
        
        return $methods;
    }
}

?>
