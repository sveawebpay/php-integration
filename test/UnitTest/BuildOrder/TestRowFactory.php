<?php

class TestRowFactory {

   /**
    * Returns ShippingFeeRow to use as shorthand in testFunctions
    * Use function run($functionname) to run shorthand function
    * @return type
    */
    function buildShippingFee(){
        return function($orderbuilder){
            return $orderbuilder
              ->addShippingFee(
                Item::shippingFee()
                     ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                );
                
        };
    }
    
    /**
    * Returns InvoicefeeRow to use as shorthand in testFunctions
    * Use function run($functionname) to run shorthand function
    * @return type
    */
    function buildInvoiceFee() {
        return function($orderbuilder) {
            return $orderbuilder
                ->addInvoiceFee(
                Item::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        );
               
        };
    }

    /**
     * Returns orderrow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return OrderBuilder

      function buildRow(){
      return function($orderBuilder) {
      return $orderBuilder
      ->beginOrderRow()
      ->setArticleNumber(1)
      ->setQuantity(2)
      ->setAmountExVat(100.00)
      ->setDesc("Specification")
      ->setName('Prod')
      ->setUnit("st")
      ->setVatPercent(25)
      ->setDiscountPercent(0)
      ->endOrderRow();
      };
      }
     * 
     */
    /**
     *  Returns FixedDiscountRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildFixedDiscountRow(){
      return function ($orderbuilder){
      return $orderbuilder
      ->beginFixedDiscount()
      ->setDiscountId("1")
      ->setAmount(100.00)
      ->setUnit("st")
      ->setDesc("FixedDiscount")
      ->setName("Fixed")
      ->endFixedDiscount(0);
      };
      }
     * 
     */
    /**
     *  Returns RelativeDiscountRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildRelativeDiscountRow(){
      return function($orderbuilder){
      return $orderbuilder
      ->beginRelativeDiscount()
      ->setDiscountId("1")
      ->setDiscountPercent(50)
      ->setUnit("st")
      ->setName('Relative')
      ->setDesc("RelativeDiscount")
      ->endRelativeDiscount();
      };
      }
     * 
     */

    /**
     *  Returns CustomerIdentity to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type

      function buildCustomerIdentity(){
      return function ($orderbuilder){
      return $orderbuilder
      ->beginIndividualCustomerIdentity()
      ->setSsn(194605092222)
      ->setInitials("SB")
      ->setBirthDate(1923, 12, 12)
      ->setName("Tess", "Testson")
      ->setEmail("test@svea.com")
      ->setPhoneNumber(999999)
      ->setIpAddress("123.123.123")
      ->setStreetAddress("Gatan",23)
      ->setCoAddress("c/o Eriksson")
      ->setZipCode(9999)
      ->setLocality("Stan")
      ->endIndividualCustomerIdentity();
      };
      }
     * 
     */
}

?>
