<?php
namespace Svea\WebService\WebServiceSoap;

/**
 * Use to transform order object into array if needed for soapClient
 */
class SveaSoapArrayBuilder {

    /**
     * Turns firs level objects in object to arrays
     * @param type $object
     * @return request, type array
     */
    /*
      public function __construct($object) {
      return $this->object_to_array($object);
      }
     *
     */

    function object_to_array($data) {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->object_to_array($value);
            }

            return $result;
        }

        return $data;
    }
}
