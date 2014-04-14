<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Parent of CloseOrder, DeliverInvoice, DeliverPaymentPlan
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HandleOrder {

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
        return new SveaAuth( 
                    $this->orderBuilder->conf->getUsername($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getPassword($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getClientNumber($this->orderBuilder->orderType,  $this->orderBuilder->countryCode)
                )
        ;
    }

    public function validateRequest() {
        $validator = new HandleOrderValidator();
        $errors = $validator->validate($this->orderBuilder);
        return $errors;
    }
}
