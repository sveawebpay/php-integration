<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * The class is used as Default to get the settings in SveaConfig
 * The class can be used as an example when creating your own
 * class implementing the ConfigurationProvider interface.
 * The class should return the right authorization values
 * and is called by the integration package.
 *
 * @author anne-hal
 */
class SveaConfigurationProvider implements ConfigurationProvider {
    
    public $conf;

    public function __construct($enviromentConfig) {
        $this->conf = (array)$enviromentConfig;
    }
/**
 * 
 * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
 * @param type $country
 * @return Username
 * @throws Exception
 */
    public function getUsername($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
        if(array_key_exists($uCountry,$this->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        
        return $this->conf['credentials'][$uCountry]['auth'][$uType]['username'];
    }

    /**
     * 
     * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
     * @param type $country
     * @return Password
     * @throws Exception
     */
    public function getPassword($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
        if(array_key_exists($uCountry,$this->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf['credentials'][$uCountry]['auth'][$uType]['password'];
    }
    /**
     * 
     * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
     * @param type $country
     * @return ClientNumber
     * @throws Exception
     */
    public function getClientNumber($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf['credentials'][$uCountry]['auth'][$uType]['clientNumber'];
    }
    /**
     * 
     * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
     * @param type $country
     * @return MerchantId
     * @throws Exception
     */
    public function getMerchantId($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf['credentials'][$uCountry]['auth'][$uType]['merchantId'];
    }
    /**
     * 
     * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
     * @param type $country
     * @return Secret word
     * @throws Exception
     */
    public function getSecret($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf['credentials'][$uCountry]['auth'][$uType]['secret'];
    }
    /**
     * 
     * @param type $type eg. INVOICE, PAYMENTPLAN, HOSTED
     * @return type
     * @throws Exception
     */
    public function getEndPoint($type) {
        $uType = strtoupper($type);
        if(array_key_exists($uType,$this->conf['url']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf['url'][$uType];
    }

   
    
}

?>
