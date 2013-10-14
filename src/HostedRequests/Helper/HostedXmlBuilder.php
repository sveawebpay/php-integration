<?php
namespace Svea;

/**
 * Rewrites formatted object to xml format to put in form element and send to external service.
 */
class HostedXmlBuilder {

    /**
     * @var XMLWriter
     */
    private $XMLWriter;
    private $isCompany = "FALSE";

    /**
     * @param  $order
     * This method expect UTF-8 input
     */
    public function getOrderXML($request, $order) {
        $this->setBaseXML();
        $this->XMLWriter->startElement("payment");
        $this->XMLWriter->writeElement("customerrefno", $order->clientOrderNumber);
        $this->XMLWriter->writeElement("currency", $request['currency']);
        $this->XMLWriter->writeElement("amount", round($request['amount']));

        if ($request['totalVat'] != null) {
            $this->XMLWriter->writeElement("vat", round($request['totalVat']));
        }

        $this->XMLWriter->writeElement("addinvoicefee", "FALSE");
        $this->XMLWriter->writeElement("lang", $request['langCode']);
        $this->XMLWriter->writeElement("returnurl", $request['returnUrl']);
        $this->XMLWriter->writeElement("cancelurl", $request['cancelUrl']);
        if($request['callbackUrl'] != null){
            $this->XMLWriter->writeElement("callbackurl", $request['callbackUrl']);
        }

        $this->serializeCustomer($order,$request);

        if (isset($order->ipAddress)) {
             $this->XMLWriter->writeElement("ipaddress", $order->ipAddress);
        }

        $this->XMLWriter->writeElement("iscompany", $this->isCompany);

        $this->serializeOrderRows($request['rows']);

        if (isset($request['excludePaymentMethods'])) {
            $this->serializeExcludePayments($request['excludePaymentMethods']);
        }

        if (isset($request['paymentMethod'])) {
            $this->XMLWriter->writeElement("paymentmethod", $request['paymentMethod']);
        }

        /*
          $this->serializeMap($order->params);
         */

        $this->XMLWriter->endElement();
        $this->XMLWriter->endDocument();

        return $this->XMLWriter->flush();
    }

    private function serializeCustomer($order,$request) {
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

        if (isset($order->customerIdentity->phonenumber)) {
             $this->XMLWriter->writeElement("phone", $order->customerIdentity->phonenumber);
        }

        if (isset($order->customerIdentity->email)) {
             $this->XMLWriter->writeElement("email", $order->customerIdentity->email);
        }

        if (isset($order->customerIdentity->street)) {
            $this->XMLWriter->writeElement("address", $order->customerIdentity->street);
        }

        if (isset($order->customerIdentity->housenumber)) {
            $this->XMLWriter->writeElement("housenumber", $order->customerIdentity->housenumber);
        }

        if (isset($order->customerIdentity->coAddress)) {
            $this->XMLWriter->writeElement("address2", $order->customerIdentity->coAddress);
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

        if (isset($order->customerIdentity->orgNumber)|| isset($order->customerIdentity->companyVatNumber)) {
            if (isset($order->customerIdentity->orgNumber)) {
                 $this->XMLWriter->writeElement("ssn", $order->customerIdentity->orgNumber);
            } else {
                  $this->XMLWriter->writeElement("vatnumber", $order->customerIdentity->companyVatNumber);
            }

            $this->isCompany = "TRUE";
        }

        $this->XMLWriter->endElement();

        if (isset($order->customerIdentity->addressSelector)) {
             $this->XMLWriter->writeElement("addressid", $order->customerIdentity->addressSelector);
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
            $this->XMLWriter->writeElement("amount", round($orderRow->amount));
        } else {
              $this->XMLWriter->writeElement("amount", "0");
        }

        if (!empty($orderRow->vat) && $orderRow->vat != null) {
            $this->XMLWriter->writeElement("vat", round($orderRow->vat));
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

    private function setBaseXML(){
        $this->XMLWriter = new \XMLWriter();
        $this->XMLWriter->openMemory();
        $this->XMLWriter->setIndent(true);
        $this->XMLWriter->startDocument("1.0", "UTF-8");
    }

    public function getPaymentMethodsXML($merchantId){
        $this->setBaseXML();
        $this->XMLWriter->startElement("getpaymentmethods");
        $this->XMLWriter->writeElement("merchantid",$merchantId);
        $this->XMLWriter->endElement();
        $this->XMLWriter->endDocument();

        return $this->XMLWriter->flush();
    }
}
