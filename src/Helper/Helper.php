<?php
namespace Svea;

/**
 * Class Helper contains various utility functions used by Svea php integration package
 *
 * @author Kristian Grossman-Madsen
 */
class Helper {

    /**
     * w/PHP_ROUND_HALF_EVEN instead
     */
    static function bround($dVal,$iDec=0) {
        return round($dVal,$iDec,PHP_ROUND_HALF_EVEN);
    }

    /**
     * Takes a total discount value ex. vat, a mean tax rate & an array of allowed tax rates.
     * returns an array of FixedDiscount objects representing the discount split
     * over the allowed Tax Rates, defined using AmountExVat & VatPercent.
     *
     * Note: only supports two allowed tax rates for now.
     * 
     * @deprecated -- use Helper::splitMeanAcrossTaxRates() instead
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
                $seenRate = Helper::bround( (($orderRow->amountIncVat - $orderRow->amountExVat) / $orderRow->amountExVat),2) *100;
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
        //Separates the street from the housenumber according to testcases, handles unicode combined code points
        $pattern = 
                "/^".                       // start of string
                "(?:\s)*".                  // non-matching group, consumes any leading whitespace
                "(\X*?)?".                  // streetname group, lazy match of any graphemes
                "(?:[\s,])+".               // non-matching group, 1+ separating whitespace or comma
                "(\pN+\X*?)?".              // housenumber group, something staring with 1+ number, followed w/lazy match of any graphemes
                "(?:\s)*".                  // non-matching group, consumes any trailing whitespace
                "$/u"                       // end of string, use unicode
        ;       
        preg_match($pattern, $address, $addressArr);
        
        // fallback if no match w/regexp
        if( !array_key_exists( 2, $addressArr ) ) { $addressArr[2] = ""; }          //fix for addresses w/o housenumber
        if( !array_key_exists( 1, $addressArr ) ) { $addressArr[1] = $address; }    //fix for no match, return entire input as streetname
        if( !array_key_exists( 0, $addressArr ) ) { $addressArr[0] = $address; }    

        return $addressArr;
    }        
    
    /**
     * Parses the src/docs/info.json file and returns associative array containing Svea integration package (library) name, version et al.
     * array contains keys "library_name" and "library_version"
     */
    static function getSveaLibraryProperties() { 
        if (!defined('SVEA_REQUEST_DIR')) {
            define('SVEA_REQUEST_DIR', dirname(__FILE__));
        }
        $info_json = file_get_contents(SVEA_REQUEST_DIR . "/docs/info.json");
        $library_properties = json_decode($info_json, true);
        return $library_properties;
    }

    /**
     * Checks ConfigurationProvider for getIntegrationXX() methods, and returns associative array containing Svea integration platform, version et al.
     * array contains keys "integration_platform", "integration_version", "integration_company"
     * @param ConfigurationProvider $config
     */
    static function getSveaIntegrationProperties( $config ) { 
        $integrationPlatform = 
            method_exists($config, "getIntegrationPlatform") ? $config->getIntegrationPlatform() : "Integration platform not available";
        $integrationCompany = 
            method_exists($config, "getIntegrationCompany") ? $config->getIntegrationCompany() : "Integration company not available";
        $integrationVersion = 
            method_exists($config, "getIntegrationVersion") ? $config->getIntegrationVersion() : "Integration version not available";
          
        $integration_properties = array( 
            "integration_platform" => $integrationPlatform,
            "integration_version" => $integrationVersion,
            "integration_company" => $integrationCompany 
        );
        return $integration_properties;
    }    
    
    /**
     * Given a ConfigurationProvider, return a json string containing the Svea integration package (library) 
     * and integration (from config) name, version et al. Used by HostedService requests.
     * @param ConfigurationProvider $config
     * @return string in json format
     */
    static function getLibraryAndPlatformPropertiesAsJson( $config ) {
        
        $libraryProperties = \Svea\Helper::getSveaLibraryProperties();
        $libraryName = $libraryProperties['library_name'];
        $libraryVersion =  $libraryProperties['library_version'];
        
        $integrationProperties = \Svea\Helper::getSveaIntegrationProperties($config);
        $integrationPlatform = $integrationProperties['integration_platform'];
        $integrationCompany = $integrationProperties['integration_company'];
        $integrationVersion = $integrationProperties['integration_version'];        
                         
        $properties_json =  '{' . 
                            '"X-Svea-Library-Name": "' . $libraryName . '", ' . 
                            '"X-Svea-Library-Version": "' . $libraryVersion . '", ' .              
                            '"X-Svea-Integration-Platform": "' . $integrationPlatform . '", ' .              
                            '"X-Svea-Integration-Company": "' . $integrationCompany . '", ' .              
                            '"X-Svea-Integration-Version": "' . $integrationVersion . '"' .
                            '}'
        ;           

        return $properties_json;
    }  
    
    /**
     * From a given total discount value, mean tax rate & an array of tax rates, 
     * this functions returns an array of FixedDiscount objects representing the
     * discount split across the given tax rates. The FixedDiscount rows are set
     * using setAmountIncVat & setVatPercent.
     *
     * Note: this function is limited to one or two given tax rates at most. For
     * a mean tax rate of zero, a single discount row is returned.
     */
    static function splitMeanAcrossTaxRates( $discountAmount, $discountMeanVat, $discountName, $discountDescription, $allowedTaxRates, $amountExVatFlag=true ) {

        $fixedDiscounts = array();

        if( $discountMeanVat > 0 ) {

            if( sizeof( $allowedTaxRates ) == 2) {

                // m = $discountMeanVat
                // r0 = allowedTaxRates[0]; r1 = allowedTaxRates[1]
                // m = a r0 + b r1 => m = a r0 + (1-a) r1 => m = (r0-r1) a + r1 => a = (m-r1)/(r0-r1)
                // d = $discountAmountExVat;
                // d = d (a+b) => 1 = a+b => b = 1-a

                $a = ($discountMeanVat - $allowedTaxRates[1]) / ( $allowedTaxRates[0] - $allowedTaxRates[1] );
                $b = 1 - $a;

                $discountAAmount = $discountAmount * $a * 
                    ($amountExVatFlag ? (1+($allowedTaxRates[0]/100.00)) : (1+($allowedTaxRates[0]/100.00))/(1+($discountMeanVat/100.00)));
                $discountA = \WebPayItem::fixedDiscount()
                                ->setAmountIncVat( Helper::bround($discountAAmount,2) )
                                ->setVatPercent( $allowedTaxRates[0] )
                                ->setName( isset( $discountName) ? $discountName : "" )
                                ->setDescription( (isset( $discountDescription) ? $discountDescription : "") . ' (' .$allowedTaxRates[0]. '%)' )
                ;

                $discountBAmount = $discountAmount * $b * 
                    ($amountExVatFlag ? (1+($allowedTaxRates[1]/100.00)) : (1+($allowedTaxRates[1]/100.00))/(1+($discountMeanVat/100.00)));           
                $discountB = \WebPayItem::fixedDiscount()
                                ->setAmountIncVat( Helper::bround($discountBAmount,2) )
                                ->setVatPercent(  $allowedTaxRates[1] )
                                ->setName( isset( $discountName) ? $discountName : "" )
                                ->setDescription( (isset( $discountDescription) ? $discountDescription : "") . ' (' .$allowedTaxRates[1]. '%)' )
                ;

                $fixedDiscounts[] = $discountA;
                $fixedDiscounts[] = $discountB;
            }

            elseif( sizeof( $allowedTaxRates ) == 1) {                
                $discountIncVat = $discountAmount * ($amountExVatFlag ? (1+($discountMeanVat/100.00)) : 1.0 ); // get amount inc vat if needed

                $discountA = \WebPayItem::fixedDiscount()
                    ->setAmountIncVat( Helper::bround( ($discountIncVat),2) )
                    ->setVatPercent( $allowedTaxRates[0] )
                    ->setName( isset( $discountName) ? $discountName : "" )
                    ->setDescription( (isset( $discountDescription) ? $discountDescription : "") )
                ;
                $fixedDiscounts[] = $discountA;
            }
        }
        
        // discountMeanVat <= 0;
        else {
            $discount = \WebPayItem::fixedDiscount()
                ->setAmountIncVat( Helper::bround( ($discountAmount),2) )
                ->setVatPercent( 0.0 )
                ->setName( isset( $discountName) ? $discountName : "" )
                ->setDescription( (isset( $discountDescription) ? $discountDescription : "") )
            ;
            $fixedDiscounts[] = $discount;                               
        }
        
        return $fixedDiscounts;
    }   
    
    /**
     * Calculates price per month for all available campaigns.
     *
     * This is a helper function provided to calculate the monthly price for the
     * different payment plan options for a given sum. This information may be
     * used when displaying i.e. payment options to the customer by checkout, or
     * to display the lowest amount due per month to display on a product level.
     *
     * If the ignoreMaxAndMinFlag is set to true, the returned array also 
     * contains the theoretical monthly installments for a given amount, even if
     * the campaign may not actually be available to use in a payment request, 
     * should the amount fall outside of the actual campaign min/max limit. If 
     * the flag is set to false or left out, the values array will not include 
     * such amounts, which may result in an empty values array in the result.
     * 
     * @param float $price
     * @param object $paymentPlanParamsResonseObject
     * @param boolean $ignoreMaxAndMinFlag; optional, defaults to false
     * @return Svea\WebService\PaymentPlanPricePerMonth
     *
     */
    public static function paymentPlanPricePerMonth($price, $paymentPlanParamsResponseObject, $ignoreMaxAndMinFlag = false) {
        return new WebService\PaymentPlanPricePerMonth($price, $paymentPlanParamsResponseObject, $ignoreMaxAndMinFlag);
    }    
}