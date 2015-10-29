<?php
// PaymentMethod is excluded from the Svea namespace

/**
 * PaymentMethod is a container for the various payment method constant strings
 *
 */
abstract class PaymentMethod {

    const INVOICE = 'INVOICE';
    const PAYMENTPLAN = 'PAYMENTPLAN';
    //DIRECT
    const BANKAXESS = 'BANKAXESS';
    const AKTIA_FI = 'DBAKTIAFI';
    const ALANDSBANKEN_FI = 'DBALANDSBANKENFI';
    const DANSKEBANK_SE = 'DBDANSKEBANKSE';
    const NORDEA_EE = 'DBNORDEAEE';
    const NORDEA_FI = 'DBNORDEAFI';
    const NORDEA_SE = 'DBNORDEASE';
    const POHJOLA_FI = 'DBPOHJOLAFI';
    const SAMPOFI = 'DBSAMPOFI';
    const SEB_SE = 'DBSEBSE';
    const SEBFTG_SE = 'DBSEBFTGSE';
    const SHB_SE = 'DBSHBSE';
    const SHB_FI = 'DBSHBFI';
    const SPANKKI_FI = 'DBSPANKKIFI';
    const SWEDBANK_SE = 'DBSWEDBANKSE';
    const TAPIOLA_FI = 'DBTAPIOLAFI';
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
}
