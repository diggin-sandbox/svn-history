<?php
/**
 * Diggin_Service_EventCast
 * 
 * Author: Mika Sasezaki
 * 
 * あとでじっくりなおす。
 */

class Diggin_Service_EventCast
{
    const EVENTCAST_BASE_URL = 'http://clip.eventcast.jp/api/v1/Search?';
    
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
     * コンストラクタ
     * 
     * @param Array
     */
    public function __construct($request) {
        if(!is_array($request)){
            $request = array($request);
        }
        
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
        
        return self::EVENTCAST_BASE_URL.$query;
    }
    
    /**
     * PHPオブジェクト取得
     * 
     * @pararm String URL
     * @return Array
     */
    public function getEventCastPhpArray () {
        $url = $this->getRequestUrl()."&Format=php";
        return unserialize(file_get_contents($url));
    }
    
    /**
     * キーワード、タグにセットした地名とlocation.addressが
     * 一致しているものを配列として返す
     * …にしたい
     * 
     * @return Array
     */
    public function getItems() {
        $ecArray = $this->getEventCastPhpArray(); 
//        foreach($items["Items"] as $item){
//            if (isset($this->_keyword) || isset($this->_tag)) {
//            $item["Location"]["Address"]; 
//        }
        return $ecArray["Items"];
    }
    
}