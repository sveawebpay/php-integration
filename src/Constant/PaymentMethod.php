<?php

/**
 * Constants for Hosted payments
 */
abstract class PaymentMethod {

    //PaymentMethodType::DIRECT
    const BANKAXESS = 'BANKAXESS';
    const DBAKTIAFI = 'DBAKTIAFI';
    const DBALANDSBANKENFI = 'DBALANDSBANKENFI';
    const DBDANSKEBANKSE = 'DBDANSKEBANKSE';
    const DBNORDEAEE = 'DBNORDEAEE';
    const DBNORDEAFI = 'DBNORDEAFI';
    const DBNORDEASE = 'DBNORDEASE';
    const DBPOHJOLAFI = 'DBPOHJOLAFI';
    const DBSAMPOFI = 'DBSAMPOFI';
    const DBSEBSE = 'DBSEBSE';
    const DBSEBFTGSE = 'DBSEBFTGSE';
    const DBSHBFI = 'DBSHBFI';
    const DBSHBSE = 'DBSHBSE';
    const DBSPANKKIFI = 'DBSPANKKIFI';
    const DBSWEDBANKSE = 'DBSWEDBANKSE';
    const DBTAPIOLAFI = 'DBTAPIOLAFI';
    //PaymentMethodType::CARD
    const KORTCERT = 'KORTCERT';
    const SKRILL = 'SKRILL';
   // const KORTSKRILL = 'KORTSKRILL'; //Same as SKRILL
    const KORTWN = 'KORTWN';
    //PaymentMethodType::PREPAID
    const MICRODEB = 'MICRODEB'; //prepay
    //PaymentMethodType::PSP
    const PAYGROUND = 'PAYGROUND';
    const PAYPAL = 'PAYPAL';
    //PaymentMethodType::INVOICE
    const SVEAINVOICESE = 'SVEAINVOICESE';
    const SVEASPLITSE = 'SVEASPLITSE';
    const SVEAINVOICEEU_SE = 'SVEAINVOICEEU_SE';
    const SVEAINVOICEEU_NO = 'SVEAINVOICEEU_NO';
    const SVEAINVOICEEU_DK = 'SVEAINVOICEEU_DK';
    const SVEAINVOICEEU_FI = 'SVEAINVOICEEU_FI';
    const SVEAINVOICEEU_NL = 'SVEAINVOICEEU_NL';
    const SVEAINVOICEEU_DE = 'SVEAINVOICEEU_DE';
    const SVEASPLITEU_SE = 'SVEASPLITEU_SE';
    const SVEASPLITEU_NO = 'SVEASPLITEU_NO';
    const SVEASPLITEU_DK = 'SVEASPLITEU_DK';
    const SVEASPLITEU_FI = 'SVEASPLITEU_FI';
    const SVEASPLITEU_DE = 'SVEASPLITEU_DE';
    const SVEASPLITEU_NL = 'SVEASPLITEU_NL';

}

?>
