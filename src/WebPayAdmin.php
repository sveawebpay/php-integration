<?php
// WebPayAdmin class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * WebPayAdmin provides entrypoints to administrative functions provided by Svea.
 * 
 * 
 *
 * @version 2.0.0
 * @author Kristian Grossman-Madsen for Svea WebPay
 * @package WebPay
 * @api 
 */
class WebPayAdmin {

    // HostedRequest/HandleOrder
    
    /**
     * annulTransaction is used to cancel (annul) a card transaction. The 
     * transaction must have status AUTHORIZED or CONFIRMED at Svea. (Indicating 
     * that the transaction has not yet been captured (settled).)
     * 
     * Use the WebPayAdmin::annulTransaction() entrypoint to get an instance of
     * AnnulTransaction. Then provide more information about the transaction and
     * send the request using @see AnnulTransaction methods.
     * 
     * @param ConfigurationProvider $config
     * @return \Svea\AnnulTransaction
     */
    static function annulTransaction($config) {
        return new Svea\AnnulTransaction($config);
    }
    
    /**
     * confirmTransaction can be performed on card transaction having the status 
     * AUTHORIZED. This will result in a CONFIRMED transaction that will be
     * captured on the given capturedate.
     * 
     * Use the WebPayAdmin::confirmTransaction() entrypoint to get an instance of
     * ConfirmTransaction. Then provide more information about the transaction and
     * send the request using @see ConfirmTransaction methods.
     * 
     * @param ConfigurationProvider $configs
     * @return \Svea\ConfirmTransaction
     */
    static function confirmTransaction($config) {
        return new Svea\ConfirmTransaction($config);
    }
    
    /**
     * creditTransaction can used to credit transactions. Only transactions that
     * have reached the status SUCCESS can be credited.
     * 
     * Use the WebPayAdmin::creditTransaction() entrypoint to get an instance of
     * CreditTransaction. Then provide more information about the transaction and
     * send the request using @see CreditTransaction methods.
     * 
     * @param ConfigurationProvider $configs
     * @return \Svea\CreditTransaction
     */

    static function creditTransaction($config) {
        return new Svea\CreditTransaction($config);
    }    
    
    // WebserviceRequest/HandleOrder
    
    
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
