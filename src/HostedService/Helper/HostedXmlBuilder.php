<?php
namespace Svea\HostedService;

/**
 * Formats request xml in preparation of sending request to hosted webservice.
 * 
 * These methods writes requests to hosted payment & hosted admin services
 * as detailed in "Technical Specification WebPay v 2.6.8" as of 140403.
 * 
 * @author Kristian Grossman-Madsen
 */
class HostedXmlBuilder {

    private $XMLWriter;
    private $isCompany = "FALSE";   // set to true by serializeCustomer if needed.

    private function setBaseXML( $config ){
        $this->XMLWriter = new \XMLWriter();
        $this->XMLWriter->openMemory();
        $this->XMLWriter->setIndent(true);
        $this->XMLWriter->startDocument("1.0", "UTF-8");                                    
        $this->XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $config ) );
    }    

    /**
     * Returns the webservice payment request message xml
     * 
     * @deprecated 2.0.0 use @see getPaymentXML instead
     * @param type $request
     * @param CreateOrderBuilder $order
     * This method expect UTF-8 input
     */
    public function getOrderXML($request, $order) {
        return $this->getPaymentXML($request, $order);
    }

    /**
     * Returns the webservice payment request message xml
     * payment request structure as in "Technical Specification WebPay v 2.6.8"
     * 
     * @param HostedPayment $request
     * @param CreateOrderBuilder $order
     * @return string
     * This method expect UTF-8 input
     */
    public function getPaymentXML($request, $order) {
        $this->setBaseXML( $order->conf );
        $this->XMLWriter->startElement("payment");
        
        //paymentmethod -- optional
        if (isset($request['paymentMethod'])) {
            $this->XMLWriter->writeElement("paymentmethod", $request['paymentMethod']); // paymentmethod -- if not set, goes to paypage
        }
        //lang -- optional
        $this->XMLWriter->writeElement("lang", $request['langCode']);
        // currency
        $this->XMLWriter->writeElement("currency", $request['currency']);
        // amount
        $this->XMLWriter->writeElement("amount", \Svea\Helper::bround($request['amount']));             

        // vat -- optional
        if ($request['totalVat'] != null) {
            $this->XMLWriter->writeElement("vat", \Svea\Helper::bround($request['totalVat']));          
        }
        // customerrefno -- optional
        $this->XMLWriter->writeElement("customerrefno", $request['clientOrderNumber']);
        // returnurl -- optional
        $this->XMLWriter->writeElement("returnurl", $request['returnUrl']);
        // cancelurl -- optional
        $this->XMLWriter->writeElement("cancelurl", $request['cancelUrl']);
        // callbackurl -- optional
        if($request['callbackUrl'] != null){
            $this->XMLWriter->writeElement("callbackurl", $request['callbackUrl']);
        }
        // subscriptiontype -- optional         
        if (isset($request['subscriptionType'])) {
            $this->XMLWriter->writeElement("subscriptiontype", $request['subscriptionType']); // subscriptiontype
        }
        
        // simulatorcode
        
        // excludepaymentmethods -- in exclude element
        if (isset($request['excludePaymentMethods'])) {
            $this->serializeExcludePayments($request['excludePaymentMethods']); // excludepaymentmethods   
        }
        
        // orderrows -- in row element
        $this->serializeOrderRows($request['rows']); // orderrows
        
        // customer -- optional
        $this->serializeCustomer($order);          // customer          // -- used by Invoice payment
        $this->XMLWriter->writeElement("iscompany", $this->isCompany);  // -- used by invoice payment
        $this->XMLWriter->writeElement("addinvoicefee", "FALSE");       // -- used by invoice payment
        // iscompany -- optional
        // addinvoicefee -- optional
        // addressid -- optional                                        // -- used by invoice payment
        
        // not in specification, but seems legit
        if (isset($request['ipAddress'])) {
             $this->XMLWriter->writeElement("ipaddress", $request['ipAddress']);
        }

        $this->XMLWriter->endElement();
        $this->XMLWriter->endDocument();

        return $this->XMLWriter->flush();
    }

    /**
     * Returns the webservice preparepayment request message xml
     * uses the same code as getPaymentXML above, with the addition of the lang and ipaddress fields 
     * 
     * @param type $request
     * @param CreateOrderBuilder $order
     * @return type
     * This method expect UTF-8 input
     */
    public function getPreparePaymentXML($request, $order) {
        $this->setBaseXML( $order->conf );
        $this->XMLWriter->startElement("payment");

        if (isset($request['paymentMethod'])) {
            $this->XMLWriter->writeElement("paymentmethod", $request['paymentMethod']); // paymentmethod -- if not set, goes to paypage
        }
        $this->XMLWriter->writeElement("lang", $request['langCode']);                   // required in preparepayment 
        $this->XMLWriter->writeElement("currency", $request['currency']);
        $this->XMLWriter->writeElement("amount", \Svea\Helper::bround($request['amount']));             

        if ($request['totalVat'] != null) {
            $this->XMLWriter->writeElement("vat", \Svea\Helper::bround($request['totalVat']));          
        }
        $this->XMLWriter->writeElement("customerrefno", $request['clientOrderNumber']);
        $this->XMLWriter->writeElement("returnurl", $request['returnUrl']);
        $this->XMLWriter->writeElement("cancelurl", $request['cancelUrl']);
        if($request['callbackUrl'] != null){
            $this->XMLWriter->writeElement("callbackurl", $request['callbackUrl']);
        }
        // subscriptiontype -- optional         
        if (isset($request['subscriptionType'])) {
            $this->XMLWriter->writeElement("subscriptiontype", $request['subscriptionType']); // subscriptiontype
        }
        // simulatorcode
        if (isset($request['excludePaymentMethods'])) {
            $this->serializeExcludePayments($request['excludePaymentMethods']); // excludepaymentmethods   
        }
        $this->serializeOrderRows($request['rows']); // orderrows

        $this->XMLWriter->writeElement("ipaddress", $request['ipAddress']);     // required in preparepayment
                
        $this->serializeCustomer($order); // customer          // -- used by Invoice payment
        $this->XMLWriter->writeElement("iscompany", $this->isCompany);  // -- used by invoice payment
        $this->XMLWriter->writeElement("addinvoicefee", "FALSE");       // -- used by invoice payment
        // addressid                                                    // -- used by invoice payment
            
        $this->XMLWriter->endElement();
        $this->XMLWriter->endDocument();

        return $this->XMLWriter->flush();
    }
    
    
    private function serializeCustomer($order) {
        $this->XMLWriter->startElement("customer");
        //nordic country individual
        if (isset($order->customerIdentity->ssn)) {
            $this->XMLWriter->writeElement("ssn", $order->customerIdentity->ssn);
        } elseif (isset($order->customerIdentity->birthDate)) {
             $this->XMLWriter->writeElement("ssn", $order->customerIdentity->birthDate);
        }

        //customer identity for NL and DE when choosing invoice or paymentplan

        if (isset($order->customerIdentity->firstname)) {
             $this->XMLWriter->writeElement("firstname", $order->customerIdentity->firstname);
        }

        if (isset($order->customerIdentity->lastname)) {
             $this->XMLWriter->writeElement("lastname", $order->customerIdentity->lastname);
        }

        if (isset($order->customerIdentity->initials)) {
             $this->XMLWriter->writeElement("initials", $order->customerIdentity->initials);
        }
        
        if (isset($order->customerIdentity->street)) {
            $this->XMLWriter->writeElement("address", $order->customerIdentity->street);
        }
        
        if (isset($order->customerIdentity->coAddress)) {
            $this->XMLWriter->writeElement("address2", $order->customerIdentity->coAddress);
        }

        if (isset($order->customerIdentity->housenumber)) {
            $this->XMLWriter->writeElement("housenumber", $order->customerIdentity->housenumber);
        }

        if (isset($order->customerIdentity->zipCode)) {
            $this->XMLWriter->writeElement("zip", $order->customerIdentity->zipCode);
        }

        if (isset($order->customerIdentity->locality)) {
            $this->XMLWriter->writeElement("city", $order->customerIdentity->locality);
        }

        if (isset($order->countryCode)) {
            $this->XMLWriter->writeElement("country", $order->countryCode);
        }

        if (isset($order->customerIdentity->phonenumber)) {
             $this->XMLWriter->writeElement("phone", $order->customerIdentity->phonenumber);
        }

        if (isset($order->customerIdentity->email)) {
             $this->XMLWriter->writeElement("email", $order->customerIdentity->email);
        }
 
        if (isset($order->customerIdentity->orgNumber)|| isset($order->customerIdentity->companyVatNumber)) {
            if (isset($order->customerIdentity->orgNumber)) {
                 $this->XMLWriter->writeElement("ssn", $order->customerIdentity->orgNumber);
            } else {
                  $this->XMLWriter->writeElement("vatnumber", $order->customerIdentity->companyVatNumber); // -- used by Invoice payment
            }
            
            // companyname      // -- used by Invoice payment
            // companyid        // -- used by Invoice payment

            $this->isCompany = "TRUE";
        }

        $this->XMLWriter->endElement();

        if (isset($order->customerIdentity->addressSelector)) {
             $this->XMLWriter->writeElement("addressid", $order->customerIdentity->addressSelector);    // -- used by Invoice payment
        }
    }

    private function serializeOrderRows($orderRows) {
        if (count($orderRows) > 0) {
            $this->XMLWriter->startElement("orderrows");

            foreach ($orderRows as $orderRow) {
                $this->serializeOrderRow($orderRow);
            }

            $this->XMLWriter->endElement();
        }
    }

    private function serializeOrderRow($orderRow) {
        $this->XMLWriter->startElement("row");

        if (!empty($orderRow->sku) && $orderRow->sku != null) {
            $this->XMLWriter->writeElement("sku", $orderRow->sku);
        } else {
            $this->XMLWriter->writeElement("sku", "");
        }

        if (!empty($orderRow->name) && $orderRow->name != null) {
            $this->XMLWriter->writeElement("name", $orderRow->name);
        } else {
            $this->XMLWriter->writeElement("name", "");
        }

        if (!empty($orderRow->description) && $orderRow->description != null) {
            $this->XMLWriter->writeElement("description", $orderRow->description);
        } else {
            $this->XMLWriter->writeElement("description", "");
        }

        if (!empty($orderRow->amount) && $orderRow->amount != null) {
            $this->XMLWriter->writeElement("amount", \Svea\Helper::bround($orderRow->amount));
        } else {
              $this->XMLWriter->writeElement("amount", "0");
        }

        if (!empty($orderRow->vat) && $orderRow->vat != null) {
            $this->XMLWriter->writeElement("vat", \Svea\Helper::bround($orderRow->vat));
        } else {
            $this->XMLWriter->writeElement("vat", "0");
        }

        if (!empty($orderRow->quantity) && $orderRow->quantity != null) {
            $this->XMLWriter->writeElement("quantity", $orderRow->quantity);
        }

        if (!empty($orderRow->unit) && $orderRow->unit != null) {
            $this->XMLWriter->writeElement("unit", $orderRow->unit);
        }

        $this->XMLWriter->endElement();
    }

    private function serializeExcludePayments($payMethods) {      
        if (count($payMethods) > 0) {
            $this->XMLWriter->startElement("excludepaymentmethods");

            foreach ($payMethods as $payMethod) {
                $this->XMLWriter->writeElement('exclude', $payMethod);
            }

            $this->XMLWriter->endElement();
        }
    } 
        
    /*
     * write xml for webservice getpaymentmethods
     */
    public function getPaymentMethodsXML($merchantId){
        $this->setBaseXML();
        $this->XMLWriter->startElement("getpaymentmethods");
        $this->XMLWriter->writeElement("merchantid",$merchantId);
        $this->XMLWriter->endElement();
        $this->XMLWriter->endDocument();

        return $this->XMLWriter->flush();
    }
}
