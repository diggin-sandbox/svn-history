<?php

class Diggin_Service_Tumblr_Read_Iterator implements Iterator, Countable
{
    /**
     * @var int count page
     */
    private $count;

    /**
     * @var int 
     */
    private $totalNum;

    private $requestNum;

    /**
     * @var Diggin_Service_Tumblr_Read
     */
    private $reader;

    public static function factory($target, $params = array(), $reader = null)
    {
        if (!$reader instanceof Diggin_Service_Tumblr_Read) {
            $reader = new Diggin_Service_Tumblr_Read($target);
        }

        $totalNum = $reader->getTotal();

        if (!isset($params['num'])) {
            $params['num'] = Diggin_Service_Tumblr_Read::READ_NUM_MAX;
        }
        $requestNum = $params['num'];
        //$requestStart = $params['start'];

        $count = self::calculateCount($params['num'], $totalNum);

        $params['num'] = ($params['num'] > Diggin_Service_Tumblr_Read::READ_NUM_MAX) ?  Diggin_Service_Tumblr_Read::READ_NUM_MAX : $params['num'];

        if (!isset($params['start']) or is_int($params['start'])) $params['start'] = 0;

        return new self($reader, $params, $count, $totalNum, $requestNum);
    }

    /**
     *
     * @return int
     */
    protected static function calculateCount($num, $totalNum)
    {  
        //filtering num
        if ($num > $totalNum) $num = $totalNum;

        return (int)ceil($num / Diggin_Service_Tumblr_Read::READ_NUM_MAX);
    }

    private function __construct($reader, $params, $count, $totalNum, $requestNum)
    {  
        $this->reader = $reader;
        $this->params = $params;
        $this->count = $count;
        $this->totalNum = $totalNum;
        $this->requestNum = $requestNum;
    }

    public function current()
    {  
        //end ?
        if ($this->params['start'] === ($this->count - 1)) {
            $rest = $this->requestNum % Diggin_Service_Tumblr_Read::READ_NUM_MAX;
            if (0 !== $rest) $this->params['num'] = $rest;
        }
        // do HTTP request
        return $this->reader->getPosts($this->params);
    }

    public function next()
    {  
        $this->params['start'] = $this->params['start'] + 1;
    }

    public function key()
    {  
        return $this->params['start'];
    }

    public function valid()
    {
        return (boolean)($this->params['start'] < $this->count) ;
    }

    public function rewind()
    {  
        $this->params['start'] = 0;
    }

    public function count()
    {  
        return $this->count;
    }
}
