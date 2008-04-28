<?php
/**
 * 
 * how to use *
 * $munoh = new Diggin_Service_Munoh();
 * var_dump($munoh->puchipuchi(array("action"=>"arai")));
 * 
 * $munoh = new Diggin_Service_Munoh();
 * echo ($munoh->getVoice());
 * 
 */

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

/**
 * @category   Diggin
 * @package    Diggin_Service_Munoh
 * @subpackage Munoh
 * @author     sasezaki
 */

class Diggin_Service_Munoh extends Zend_Service_Abstract
{
    
    const API_URL = 'http://m.unoh.net/puchipuchi';

    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected $_client;
    
    /**
     * Microtime of last request
     *
     * @var float
     */
    protected static $_lastRequestTime = 0;
    
    public function getApiUrl()
    {
        return self::API_URL;
    }
    
    /**
     * puchipuchi!
     * 
     * @param array
     * @return SimpleXMLIterator
     */
    public function puchipuchi(array $parms = null) {
        $response = $this->makeRequest($parms);
        return $this->_xmlResponseToSimpleXMLIterator($response);
    }
    
    /**
     * Handles all POST requests to a web service
     *
     * @param  array  $parms Array of POST parameters
     * @return mixed  decoded response from web service
     */
    public function makeRequest(array $parms = null)
    {
        // if previous request was made less then 1 sec ago
        // wait until we can make a new request
        $timeDiff = microtime(true) - self::$_lastRequestTime;
        if ($timeDiff < 1) {
            usleep((1 - $timeDiff) * 1000000);
        }

        $this->_client = self::getHttpClient();
        $this->_client->setUri(self::getApiUrl());
        if(isset($parms)){
            $this->_client->setParameterPOST($parms);
        }
        
        self::$_lastRequestTime = microtime(true);
        $response = $this->_client->request('POST');

        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Munoh_Exception
              */
             require_once 'Diggin/Service/Munoh/Exception.php';
             throw new Diggin_Service_Munoh_Exception("Http client reported an error: '{$response->getMessage()}'");
        }
        
        $responseBody = $response->getBody();
                
        $dom = new DOMDocument() ;

        if (!@$dom->loadXML($responseBody)) {
           /**
            * @see Diggin_Service_Tumblr_Exception
            */
           require_once 'Diggin/Service/Munoh/Exception.php';
           throw new Diggin_Service_Munoh_Exception('XML Error');
        }
    
        return $dom;
    }
    
    public function getVoice(array $parms = null){
        $xml = $this->puchipuchi($parms);

        return (string) $xml->voice;
    }
    
    /**
     * Transform XML string to SimpleXMLIterator
     *
     * @param  DOMDocument $response
     * @return SimpleXMLIterator
     */
//    private static function _xmlResponseToPostArray(DOMDocument $response)
    private static function _xmlResponseToSimpleXMLIterator(DOMDocument $response)
    {
        $sxml = simplexml_import_dom($response, 'SimpleXMLIterator');
                
        return $sxml;
    }
    
//    private function _xmlIteration(SimpleXMLIterator $obj)
//    {
//        foreach ($obj as $key => $value) {
//            $ret['key'] = $key;
//            $ret['value'] = $value;
//            if($obj->hasChildren()) {
//                $this->_xmlIteration($obj->getChildren());                    
//            }
//        }
//        
//        return;
//    }
}
