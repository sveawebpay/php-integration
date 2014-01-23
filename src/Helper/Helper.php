<?php
namespace Svea;

/**
 * Class Helper contains various utility functions used by Svea php integration package
 *
 * @author Kristian Grossman-Madsen
 */
class Helper {

    /**
     * taken from http://boonedocks.net/code/bround-bsd.inc.phps,
     * licensed under the bsd license
     */

    static function bround($dVal,$iDec=0) {
        // banker's style rounding or round-half-even
        // (round down when even number is left of 5, otherwise round up)
        // $dVal is value to round
        // $iDec specifies number of decimal places to retain
        static $dFuzz=0.00001; // to deal with floating-point precision loss
        $iRoundup=0; // amount to round up by

        $iSign=($dVal!=0.0) ? intval($dVal/abs($dVal)) : 1;
        $dVal=abs($dVal);

        // get decimal digit in question and amount to right of it as a fraction
        $dWorking=$dVal*pow(10.0,$iDec+1)-floor($dVal*pow(10.0,$iDec))*10.0;
        $iEvenOddDigit=floor($dVal*pow(10.0,$iDec))-floor($dVal*pow(10.0,$iDec-1))*10.0;

        if (abs($dWorking-5.0)<$dFuzz) $iRoundup=($iEvenOddDigit & 1) ? 1 : 0;
        else $iRoundup=($dWorking>5.0) ? 1 : 0;

        return $iSign*((floor($dVal*pow(10.0,$iDec))+$iRoundup)/pow(10.0,$iDec));
    }

    /**
     * Takes a total discount value ex. vat, a mean tax rate & an array of allowed tax rates.
     * returns an array of FixedDiscount objects representing the discount split
     * over the allowed Tax Rates, defined using AmountExVat & VatPercent.
     *
     * Note: only supports two allowed tax rates for now.
     */
    static function splitMeanToTwoTaxRates( $discountAmountExVat, $discountMeanVat, $discountName, $discountDescription, $allowedTaxRates ) {

        $fixedDiscounts = array();

        if( sizeof( $allowedTaxRates ) > 1 ) {

            // m = $discountMeanVat
            // r0 = allowedTaxRates[0]; r1 = allowedTaxRates[1]
            // m = a r0 + b r1 => m = a r0 + (1-a) r1 => m = (r0-r1) a + r1 => a = (m-r1)/(r0-r1)
            // d = $discountAmountExVat;
            // d = d (a+b) => 1 = a+b => b = 1-a

            $a = ($discountMeanVat - $allowedTaxRates[1]) / ( $allowedTaxRates[0] - $allowedTaxRates[1] );
            $b = 1 - $a;

            $discountA = \WebPayItem::fixedDiscount()
                            ->setAmountExVat( Helper::bround(($discountAmountExVat * $a),2) )
                            ->setVatPercent( $allowedTaxRates[0] )
                            ->setName( isset( $discountName) ? $discountName : "" )
                            ->setDescription( (isset( $discountDescription) ? $discountDescription : "") . ' (' .$allowedTaxRates[0]. '%)' )
            ;

            $discountB = \WebPayItem::fixedDiscount()
                            ->setAmountExVat( Helper::bround(($discountAmountExVat * $b),2) )
                            ->setVatPercent(  $allowedTaxRates[1] )
                            ->setName( isset( $discountName) ? $discountName : "" )
                            ->setDescription( (isset( $discountDescription) ? $discountDescription : "") . ' (' .$allowedTaxRates[1]. '%)' )
            ;

            $fixedDiscounts[] = $discountA;
            $fixedDiscounts[] = $discountB;

        }
        // single tax rate, so use shop supplied mean as vat rate
        else {
            $discountA = \WebPayItem::fixedDiscount()
                ->setAmountExVat( Helper::bround(($discountAmountExVat),2) )
                ->setVatPercent( $allowedTaxRates[0] )
                ->setName( isset( $discountName) ? $discountName : "" )
                ->setDescription( (isset( $discountDescription) ? $discountDescription : "") )
            ;
            $fixedDiscounts[] = $discountA;
        }
        return $fixedDiscounts;
    }






    /**
     * Takes a createOrderBuilder object, iterates over its orderRows, and
     * returns an array containing the distinct taxrates present in the order
     */
    static function getTaxRatesInOrder($order) {
        $taxRates = array();

        foreach( $order->orderRows as $orderRow ) {

            if( isset($orderRow->vatPercent) ) {
                $seenRate = $orderRow->vatPercent; //count
            }
            elseif( isset($orderRow->amountIncVat) && isset($orderRow->amountExVat) ) {
                $seenRate = Helper::bround( (($orderRow->amountIncVat - $orderRow->amountExVat) / $orderRow->amountExVat) ,2) *100;
            }

            if(isset($seenRate)) {
                isset($taxRates[$seenRate]) ? $taxRates[$seenRate] +=1 : $taxRates[$seenRate] =1;   // increase count of seen rate
            }
        }
        return array_keys($taxRates);   //we want the keys
    }

    /**
     * Takes a streetaddress string and splits the streetname and the housenumber, returning them in an array
     * Handles many different street address formats, see test suite SplitAddressTest.php test cases for examples.
     * 
     * If no match found, will return input streetaddress in position 0 and streetname, empty string in housenumber positions.
     * 
     * @param string $address --
     * @return string -- array with the entire streetaddress in position 0, the streetname in position 1 and housenumber in position 2 
     */
    static function splitStreetAddress($address){
        //Seperates the street from the housenumber according to testcases
        $pattern = "/^(?:\s)*([0-9]*[A-ZÄÅÆÖØÜßäåæöøüa-z]*\s*[A-ZÄÅÆÖØÜßäåæöøüa-z]+)(?:[\s,]*)([0-9]*\s*[A-ZÄÅÆÖØÜßäåæöøüa-z]*(?:\s*[0-9]*)?[^\s])?(?:\s)*$/";       
        
        preg_match($pattern, $address, $addressArr);
        
        // fallback if no match w/regexp
        if( !array_key_exists( 2, $addressArr ) ) { $addressArr[2] = ""; }  //fix for addresses w/o housenumber
        if( !array_key_exists( 1, $addressArr ) ) { $addressArr[1] = $address; }    //fixes for no match at all, return complete input in streetname
        if( !array_key_exists( 0, $addressArr ) ) { $addressArr[0] = $address; }    

        return $addressArr;
    }

}