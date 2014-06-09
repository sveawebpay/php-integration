<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Parent of CloseOrder, DeliverInvoice, DeliverPaymentPlan
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
abstract class HandleOrder {

    /** CloseOrderBuilder|DeliverOrderBuilder $handler  object containing the settings for the HandleOrder request */
    public $orderBuilder;

    /**
     * @param CloseOrderBuilder|DeliverOrderBuilder $handleOrderBuilder
     */
    public function __construct($handleOrderBuilder) {
        $this->orderBuilder = $handleOrderBuilder;
    }

    /** 
     * creates a SveaAuth object using the passed orderBuilder configuration
     * @return SveaAuth
     */
    protected function getStoreAuthorization() {
        return new WebServiceSoap\SveaAuth( 
                    $this->orderBuilder->conf->getUsername($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getPassword($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getClientNumber($this->orderBuilder->orderType,  $this->orderBuilder->countryCode)
                )
        ;
    }

    public $errors = array();
    
    /**
     * Validates the orderBuilder object to make sure that all required settings
     * are present. If not, throws an exception. Actual validation is delegated
     * to subclass validate() implementations.
     *
     * @throws ValidationException
     */
    public function validateRequest() {
        $errors = $this->validate($this->orderBuilder);             
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new \Svea\ValidationException($exceptionString);
        }    
    }       
    
    abstract function validate($orderBuilder); // validate is defined by subclasses, should validate all order fields required for call is present
}
