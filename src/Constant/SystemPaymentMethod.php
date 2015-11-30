<?php
namespace Svea;

/**
 * Constants for Hosted payments
 */
abstract class SystemPaymentMethod {

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
    const SVEACARDPAY = 'SVEACARDPAY';

    const SKRILL = 'SKRILL';
    const KORTWN = 'KORTWN';
    //PREPAID
    const MICRODEB = 'MICRODEB';
    //PSP
    const PAYGROUND = 'PAYGROUND';
    const PAYPAL = 'PAYPAL';
    //PaymentMethodType::INVOICE
    const INVOICESE = 'SVEAINVOICESE';
    const PAYMENTPLANSE = 'SVEASPLITSE';
    const INVOICE_SE = 'SVEAINVOICEEU_SE';
    const INVOICE_NO = 'SVEAINVOICEEU_NO';
    const INVOICE_DK = 'SVEAINVOICEEU_DK';
    const INVOICE_FI = 'SVEAINVOICEEU_FI';
    const INVOICE_NL = 'SVEAINVOICEEU_NL';
    const INVOICE_DE = 'SVEAINVOICEEU_DE';
    //PaymentMethodType::PAYMENTPLAN
    const PAYMENTPLAN_SE = 'SVEASPLITEU_SE';
    const PAYMENTPLAN_NO = 'SVEASPLITEU_NO';
    const PAYMENTPLAN_DK = 'SVEASPLITEU_DK';
    const PAYMENTPLAN_FI = 'SVEASPLITEU_FI';
    const PAYMENTPLAN_DE = 'SVEASPLITEU_DE';
    const PAYMENTPLAN_NL = 'SVEASPLITEU_NL';
}
