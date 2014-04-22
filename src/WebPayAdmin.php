<?php
// WebPayAdmin class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * WebPayAdmin provides entrypoints to administrative functions provided by Svea.
 *
 * @version 2.0.0
 * @author Kristian Grossman-Madsen for Svea WebPay
 * @package WebPay
 * @api 
 */
class WebPayAdmin {
    
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
