<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Service
 * @subpackage Tumblr
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';


/**
 * @category   Diggin
 * @package    Diggin_Service_Tumblr
 * @subpackage Tumblr
 * @author     sasezaki
 */

class Diggin_Service_Tumblr_Read extends Diggin_Service_Abstract
{
    
    const API_LOCATION = '.tumblr.com/api/read';
    
    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected $_client;
    
    /**
     * target
     *
     * @var string
     */
    protected $_target;

    /**
     * Microtime of last request
     *
     * @var float
     */
    protected static $_lastRequestTime = 0;

    /**
     * Constructs a new Tumblr Web Services Client
     *
     * @param  string $target subdomain of tumblr OR Domain
     * @return null
     */
    public function __construct($target = null)
    {
        $this->_target = $target;
    }

    /**
     * Set target
     *
     * @param  string $target subdomain,domain,URL
     * @return Diggin_Service_Tumblr_Read
     */
    public function setTarget($target)
    {
        $this->_target = (string) $target;
        
        return $this;
    }
    
    /**
     * Get target
     *
     * @return string $target
     */
    public function getTarget()
    {
        return $this->_target;    
    }
        
    public function getApiUrl()
    {
        $apiUrl = 'http://'.$this->getTarget().self::API_LOCATION;
        
        return $apiUrl;
    }

    public function getTotal()
    {
        $response = $this->makeRequest();
        
        $rootNode = $response->documentElement;

        if ($rootNode->nodeName == 'tumblr') {
            $childNodes = $rootNode->childNodes;

            for ($i = 0; $i < $childNodes->length; $i++) {
                $currentNode = $childNodes->item($i);
                if ($currentNode->nodeName == 'posts') {
                    (integer)$total = $currentNode->getAttribute('total');
                }
            }
        } else {
            /**
             * @see Diggin_Service_Tumblr_Exception
             */
            require_once 'Diggin/Service/Tumblr/Exception.php';
            throw new Diggin_Service_Tumblr_Exception('Tumblr web service has returned something odd!');
        }

        return $total;
    }
    
    /*
     * get 'posts' as array
     * 
     * @param array $parms
     * @return array $posts
     */
    public function getPosts ($parms = array())
    {
        $start = 0;
        $num = 50;
        $loop = 1;
        $postsArr = array();
        
        if (isset($parms['start'])) {
            $start = $parms['start'];
        }
        
        if ($parms['num'] >= 50) {
            $loop += floor($parms['num']/$num);
            $parms['num'] = 50;
        }
        
        for ($i = 0; $i < $loop; $i++) {
            $parms['start'] = $i * $num + $start;
            $response = $this->makeRequest($parms);
            $postsArr = $postsArr + $this->_xmlResponseToPostArray($response);
        }
                    
        return $postsArr;
    }
    
    public function dumpAsXmls($path = '/workspace/', $filePrefix = 'tumblr_', $parms = array())
    {
         
        if (!$parms['num']) {
            $parms['num'] = $this->getTotal(); 
        }      

        $start = 0;
        $num = 50;
        $loop = 1;
        $postsArr = array();
        
        if ($parms['start']) {
            $start = $parms['start'];
        }
        
        if ($parms['num'] >= 50) {
            $loop += floor($parms['num']/$num);
            $parms['num'] = 50;
        }
        
        for ($i = 0; $i < $loop; $i++) {
            $parms['start'] = $i * $num + $start;
            $response = $this->makeRequest($parms);
            $response->save($path.$filePrefix.$i.'.xml');
        }
        
    }
    
    
    //This is test method
    public function getAllPhotoUrl()
    {
        $parms['type'] = 'photo';
        
        $parms['num'] = $this->getTotal();
        $posts = $this->getPosts($parms);
        foreach ($posts as $post) {
            $arrPhotoUrl[] = $post['photo-url'];
        }     
         
        return $arrPhotoUrl;
    }
    
        
    /**
     * Handles all GET requests to a web service
     *
     * @param  array  $parms Array of GET parameters
     * @return mixed  decoded response from web service
     */
    public function makeRequest(array $parms = array())
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
        $this->_client->setParameterGet($parms);
        }
        
        self::$_lastRequestTime = microtime(true);
        $response = $this->_client->request('GET');
        
        if (!$response->isSuccessful()) {
             /**
              * @see Diggin_Service_Tumblr_Exception
              */
            require_once 'Diggin/Service/Tumblr/Exception.php';
             throw new Diggin_Service_Tumblr_Exception("Http client reported an error: '{$response->getMessage()}'");
        }

        $responseBody = $response->getBody();
        
           $dom = new DOMDocument() ;
    
           if (!@$dom->loadXML($responseBody)) {
               /**
                * @see Diggin_Service_Tumblr_Exception
                */
               require_once 'Diggin/Service/Tumblr/Exception.php';
               throw new Diggin_Service_Tumblr_Exception('XML Error');
           }
    
        return $dom;

    }
    
    /**
     * Transform XML string to array
     *
     * @param  DOMDocument $response
     * @param  string      $root     Name of root tag
     * @param  string      $child    Name of children tags
     * @param  string      $attKey   Attribute of child tag to be used as a key
     * @param  string      $attValue Attribute of child tag to be used as a value
     * @return array
     */
    private static function _xmlResponseToPostArray(DOMDocument $response, $maxWidth = '500')
    {
        $child = 'posts';   //childには　tumblelog, posts
        $arrOut = array();
        $rootNode = $response->documentElement;
        
        $childNodes = $rootNode->childNodes;

        for ($i = 0; $i < $childNodes->length; $i++) {
                $currentNode = $childNodes->item($i);
                if ($currentNode->nodeName == $child) {
                    
                    for ($n = 0; $n < $currentNode->childNodes->length; $n++){
                        $postNode = $currentNode->childNodes->item($n);
                        
                        $id = $postNode->getAttribute('id');
                        $arrOut[$id]['id'] = $postNode->getAttribute('id');
                        $arrOut[$id]['url'] = $postNode->getAttribute('url');
                        $arrOut[$id]['type'] = $postNode->getAttribute('type');
                        $arrOut[$id]['date'] = $postNode->getAttribute('date');

                        for ($m = 0; $m < $postNode->childNodes->length; $m++){
                           $postChildNode = $postNode->childNodes->item($m);
                           if ($postChildNode->nodeName == 'photo-url') {
                               if($postChildNode->getAttribute('max-width') == $maxWidth){
                                   $arrOut[$id][$postChildNode->nodeName] = $postChildNode->nodeValue;
                               }
                           } else  {
                                   $arrOut[$id][$postChildNode->nodeName] = $postChildNode->nodeValue;
                           }
                        }
                    }
                }

        }
        
        return $arrOut;
    }

}
