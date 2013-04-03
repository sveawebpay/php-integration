# Changelog

## 0.0.6
* Namechange: 
    * setCompanyIdNumber() to setNationalIdNumber()
    * setSsn() to setNationalIdNumber()
    
* Added
    * ->setPayPageLanguage() when choosing one of the PayPagePayments

* Changed
    * Paymentmethods in APPENDIX are fewer and more generalized
    * Validation prevents companies to use Payment plan payment
    * Country code is required for all payments

##0.1.0
 * Config
    * SveaConfig can hold multiple authorization values depending on country, paymenttype, testmode.
    * ConfigurationProvider.php interface to build custom ConfigurationProvider.
    * SveaConfigurationProvider implements interface ConfigurationProvider and provides package with authoriazation values from SveaConfig.php