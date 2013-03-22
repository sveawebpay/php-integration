<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigurationProvider
 *
 * @author anne-hal
 */
class SveaConfigurationProvider implements ConfigurationProvider {
    
    public $conf;

    public function __construct($enviromentConfig) {
        $this->conf = $enviromentConfig;
    }

    public function getUsername($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
        if(array_key_exists($uCountry,$this->conf->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        
        return $this->conf->conf['credentials'][$uCountry]['auth'][$uType]['username'];
    }

    public function getPassword($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
        if(array_key_exists($uCountry,$this->conf->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf->conf['credentials'][$uCountry]['auth'][$uType]['password'];
    }
    public function getClientNumber($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf->conf['credentials'][$uCountry]['auth'][$uType]['clientNumber'];
    }
    public function getMerchantId($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf->conf['credentials'][$uCountry]['auth'][$uType]['merchantId'];
    }

    public function getSecret($type, $country) {
        $uType = strtoupper($type);
        $uCountry = strtoupper($country);
         if(array_key_exists($uCountry,$this->conf->conf['credentials']) == FALSE){
           throw new Exception('Invalid or missing Country code');
        }elseif(array_key_exists($uType,$this->conf->conf['credentials'][$uCountry]['auth']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf->conf['credentials'][$uCountry]['auth'][$uType]['secret'];
    }

    public function getEndPoint($type) {
        $uType = strtoupper($type);
        if(array_key_exists($uType,$this->conf->conf['url']) == FALSE){
            throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
        }
        return $this->conf->conf['url'][$uType];
    }

   
    
}

?>
