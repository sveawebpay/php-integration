<?php
namespace Svea;

/**
 * Class Helper contains various utility functions used by Svea php integration package
 *
 * @author Kristian Grossman-Madsen
 */
class Helper {

    /**
     * @deprecated -- use round() w/PHP_ROUND_HALF_EVEN instead
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
                            ->setAmountExVat( round(($discountAmountExVat * $a),2,PHP_ROUND_HALF_EVEN) )
                            ->setVatPercent( $allowedTaxRates[0] )
                            ->setName( isset( $discountName) ? $discountName : "" )
                            ->setDescription( (isset( $discountDescription) ? $discountDescription : "") . ' (' .$allowedTaxRates[0]. '%)' )
            ;

            $discountB = \WebPayItem::fixedDiscount()
                            ->setAmountExVat( round(($discountAmountExVat * $b),2,PHP_ROUND_HALF_EVEN) )
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
                ->setAmountExVat( round(($discountAmountExVat),2,PHP_ROUND_HALF_EVEN) )
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
                $seenRate = round( (($orderRow->amountIncVat - $orderRow->amountExVat) / $orderRow->amountExVat),2,PHP_ROUND_HALF_EVEN) *100;
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
}