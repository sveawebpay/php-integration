<?php

/**
 * Rewrites formatted object to xml format to put in form element and send to external service.
 */
class HostedXmlBuilder {

    /**
     * @var XMLWriter
     */
    private $XMLWriter;

    /**
     * @param  $order
     * This method expect UTF-8 input
     */
    public function getOrderXML($request, $order) {
        $this->XMLWriter = new XMLWriter();
        $this->XMLWriter->openMemory();
        $this->XMLWriter->setIndent(true);
        $this->XMLWriter->startDocument("1.0", "UTF-8");
        $this->XMLWriter->startElement("payment");
        $this->XMLWriter->writeElement("customerrefno", $order->clientOrderNumber);
        $this->XMLWriter->writeElement("returnurl", $request['returnUrl']);
        $this->XMLWriter->writeElement("cancelurl", $request['cancelUrl']);
        $this->XMLWriter->writeElement("amount", $request['amount']);
        $this->XMLWriter->writeElement("currency", $request['currency']);
        $this->XMLWriter->writeElement("lang", $request['langCode']);
        if ($request['totalVat'] != null) {
            $this->XMLWriter->writeElement("vat", $request['totalVat']);
        }
        if(isset($order->ipAddress)){
             $this->XMLWriter->writeElement("ipaddress", $order->ipAddress);
        }
        if(isset($order->ssn)){
             $this->XMLWriter->writeElement("ssn", $order->ssn);
        }

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

    private function serializeOrderRows($orderRows) {
        if (count($orderRows) > 0) {
            $this->XMLWriter->startElement("orderrows");

            foreach ($orderRows as $orderRow) {
                $this->serializeOrderRow($orderRow);
            }

            $this->XMLWriter->endElement();
        }
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

    private function serializeOrderRow($orderRow) {
        $this->XMLWriter->startElement("row");
        
        if (!empty($orderRow->description) && $orderRow->description != null) {
            $this->XMLWriter->writeElement("description", $orderRow->description);
        } else {
            $this->XMLWriter->writeElement("description", "");
        }

        if (!empty($orderRow->name) && $orderRow->name != null) {
            $this->XMLWriter->writeElement("name", $orderRow->name);
        } else {
            $this->XMLWriter->writeElement("name", "");
        }
        
        if (!empty($orderRow->sku) && $orderRow->sku != null) {
            $this->XMLWriter->writeElement("sku", $orderRow->sku);
        } else {
            $this->XMLWriter->writeElement("sku", "");
        }
        
        if (!empty($orderRow->amount) && $orderRow->amount != null) {
            $this->XMLWriter->writeElement("amount", $orderRow->amount);
        }
        
        if (!empty($orderRow->vat) && $orderRow->vat != null) {
            $this->XMLWriter->writeElement("vat", $orderRow->vat);
        }
        
        if (!empty($orderRow->unit) && $orderRow->unit != null) {
            $this->XMLWriter->writeElement("unit", $orderRow->unit);
        }
        
        if (!empty($orderRow->quantity) && $orderRow->quantity != null) {
            $this->XMLWriter->writeElement("quantity", $orderRow->quantity);
        }

        $this->XMLWriter->endElement();
    }
}

?>
