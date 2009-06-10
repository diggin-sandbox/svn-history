<?php
class Diggin_RobotRules_Protocol_Txt implements Iterator
{
    private $_robotstxt = array();
    private $_line = 0;
    private $_key = 0;

    public function __construct($robotstxt = '')
    {
        if ($robotstxt){
            $this->_toArray($robotstxt);
        }
    }
    
    protected function _toArray($robotstxt)
    {
        
        $robotstxt = str_replace(chr(13).chr(10), chr(10), $robotstxt);
        $robotstxt = str_replace(array(chr(10), chr(13)), PHP_EOL, $robotstxt);

        $robotstxt = explode(PHP_EOL, $robotstxt);

        $this->_robotstxt = $robotstxt;
    }


    public function current()
    {
        //@not_todoif none 'User-Agent: line' is handled as *
        // はgetRecordされたときにおこなう
        //@not_todo discard not follow User-Agent_line
        //@not_todo PHP_EOL+ replace single
        $record = new Diggin_RobotRules_Protocol_Txt_Record;
        //$record = array(); 
        do {
            //$record[] = Diggin_RobotRules_Protocol_Txt_Line::parse($this->_robotstxt[$this->_line]);
            $record->append(Diggin_RobotRules_Protocol_Txt_Line::parse($this->_robotstxt[$this->_line]));

            $this->_line++;
        } while (preg_match('!\w:!', $this->_robotstxt[$this->_line]));

        return $record;
    }

    public function next()
    {
        do {
            $this->_line++;
        } while ($this->valid() && !preg_match('!\w:!', $this->_robotstxt[$this->_line]));
        $this->_key++;
    }

    public function valid()
    {
        return ($this->_line < count($this->_robotstxt)) ? true : false;
    }

    public function key()
    {
        return $this->_key;
    }

    public function rewind()
    {
        $this->_line = 0;
    }
}
