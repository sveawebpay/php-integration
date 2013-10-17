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
    
}