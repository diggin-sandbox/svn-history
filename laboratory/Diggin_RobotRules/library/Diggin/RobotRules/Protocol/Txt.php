<?php
class Diggin_RobotRules_Protocol_Txt //implements
{
    private $_robotstxt = array();
    private $_line = 0;

    public function __construct($robotstxt = '')
    {
        
    }
    
    public function toArray(array $robotstxt)
    //public static function parse($robotstxt)
    {
        
        //$robotstxt = str_replace(chr(13).chr(10), chr(10), $robotstxt);
        //$robotstxt = str_replace(array(chr(10), chr(13)), PHP_EOL, $robotstxt);

        //$robotstxt = explode(PHP_EOL, $robotstxt);
        
        //$robots = new self;

        $this->_robotstxt = $robotstxt;
    }


    public function current()
    {
        //@not_todoif none 'User-Agent: line' is handled as *
        // はgetRecordされたときにおこなう
        //@not_todo discard not follow User-Agent_line
        //@not_todo PHP_EOL+ replace single
        $record = new Diggin_RobotRules_Record;
        $record = array(); 
        do {
            $record[] = $this->_robotstxt[$this->_line];
            $this->_line++;
        } while (preg_match('!\w:!', $this->_robotstxt[$this->_line]));

        return $record;
    }

    public function next()
    {
        do {
            $this->_line++;
        } while (!preg_match('!\w:!', $this->_robotstxt[$this->_line]));
    }
}
