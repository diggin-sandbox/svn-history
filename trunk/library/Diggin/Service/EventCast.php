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
 * @subpackage EventCast
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

class Diggin_Service_Eventcast extends Zend_Service_Abstract
{
    const API_URL = 'http://clip.eventcast.jp/api/v1/Search?';
    
    //今日：0、昨日から:-1
    const EVENTCAST_START_DATE_LINE = 0;
    const EVENTCAST_END_DATE_LINE = 30;
    const EVENTCAST_SORT = date;
    const EVENTCAST_ORDER = asc;
    const EVENTCAST_START = 1;
    const EVENTCAST_RESULTS_LIMIT = 50;
    //0:開催中のイベントを含む
    const EVENTCAST_TRIM = 0;
    
    private $request;
    
    protected $_keyword = null;
    protected $_username = null;
    protected $_tag = null;
    protected $_startdate = null;
    protected $_enddate = null;
    protected $_sort = null;
    protected $_order = null;
    protected $_start = null;
    protected $_results = null;
    protected $_trim = null;

    /**
     * Zend_Http_Client Object
     *
     * @var Zend_Http_Client
     */
    protected static $_client;
    
    /**
     * Constructs a new Eventcast Service Client
     * 
     * @param array | string $request
     */
    public function __construct(array $request)
    {        
        $this->_keyword = trim($request['keyword']);
        $this->_username = trim($request['username']);
        $this->_tag = trim($request['tag']);
        $this->_startdate = trim($request['startdate']);
        $this->_enddate = trim($request['enddate']);
        $this->_sort = trim($request['sort']);
        $this->_order = trim($request['order']);
        $this->_start = trim($request['start']);
        $this->_results = trim($request['results']);
        $this->_trim = trim($request['trim']);
    }
   
    /**
     * EventCastへのリクエスト用URL生成
     * 
     * @return String
     */
    public function getRequestUrl() {
        $query = null;
        
        if ($this->_keyword) {
            $query .= "&Keyword=".$this->_keyword;
        }
        
        if ($this->_username) {
            $query .= "&Username=".$this->_username;
        }
        
        if ($this->_tag) {
            $query .= "&tag=".$this->_tag;
        }
        
        if ($this->_startdate) {
            $query .= "&StartDate=".$this->_startdate;
        } else {
            $dateS = date('Y/m/d', time()+86400*self::EVENTCAST_START_DATE_LINE);
            $query .= "&StartDate=".$dateS;        
        }
        
        if ($this->_enddate) {
            $query .= "&EndDate=".$this->_enddate;
        } else {
            $dateE = date('Y/m/d', time()+86400*self::EVENTCAST_END_DATE_LINE);
            $query .= "&EndDate=".$dateE;        
        }
        
        if ($this->_sort) {
            $query .= "&Sort=".$this->_sort;
        } else {
            $query .= "&Sort=".self::EVENTCAST_SORT;        
        }
        
        if ($this->_order) {
            $query .= "&Order=".$this->_sort;
        } else {
            $query .= "&Order=".self::EVENTCAST_ORDER;        
        }        
        
        if ($this->_start) {
            $query .= "&Start=".$this->_start;
        } else {
            $query .= "&Start=".self::EVENTCAST_START;        
        } 
        
        if ($this->_results) {
            $query .= "&Results=".$this->_results;
        } else {
            $query .= "&Results=".self::EVENTCAST_RESULTS_LIMIT;        
        }
        
        if ($this->_trim) {
            $query .= "&Trim=".$this->_trim;
        } else {
            $query .= "&Trim=".self::EVENTCAST_TRIM;        
        }
        
        $query = substr_replace($query, '', 0, 1);
        
        return $query."&Format=php";
    }


	/**
     * Handles all GET requests to a web service
     *
     * @param  array $params  Parameter
     * @return mixed  decoded response from web service
     */
    public function makeRequest()
    {
        self::$_client = self::getHttpClient();
        
        require_once 'Zend/Uri/Http.php';
        $uri = Zend_Uri_Http::factory(self::API_URL);
        
        $uri->setQuery($this->getRequestUrl());
        
        return $uri->getUri();
    }
//    /**
//     * PHPオブジェクト取得
//     * 
//     * @pararm String URL
//     * @return Array
//     */
//    public function getEventCastPhpArray () {
//        $url = $this->getRequestUrl()."&Format=php";
//        return unserialize(file_get_contents($url));
//    }
//    
//    /**
//     * キーワード、タグにセットした地名とlocation.addressが
//     * 一致しているものを配列として返す
//     * …にしたい
//     * 
//     * @return Array
//     */
//    public function getItems() {
//        $ecArray = $this->getEventCastPhpArray(); 
////        foreach($items["Items"] as $item){
////            if (isset($this->_keyword) || isset($this->_tag)) {
////            $item["Location"]["Address"]; 
////        }
//        return $ecArray["Items"];
//    }
}