<?php
/** Diggin_RobotRules_Line **/
require_once 'Line.php';
/** Diggin_RobotRules_Record **/
require_once 'Record.php';

/**
class Diggin_RobotRules implements Iterator, Countable
{
    private $_count = 0;
    //public function parse(){}

    public function append(Diggin_RobotRules_Record $record)
    {
        return;
    }
}
*/


class Diggin_RobotRules
{
    private $_robotstxt = array();
    private $_line = 0;

    public function set(array $robotstxt)
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


class Diggin_RobotRules_Parser
{

    //private $_robotstxt;
    
    const MULTIBYTESPACE = ' ';

    /**
     * parse robots.txt context
     *
     * @param string $robotstxt
     * @return array $rules 
     */
    public static function parse($robotstxt)
    {
        // Handle LINE
        $robotstxt = str_replace(chr(13).chr(10), chr(10), $robotstxt);
        $robotstxt = str_replace(array(chr(10), chr(13)), PHP_EOL, $robotstxt);

        $robotstxt = explode(PHP_EOL, $robotstxt);

        $robots = new Diggin_RobotRules;
        $robots->set($robotstxt);

        return $robots;
    }

    public static function check($robotstxt)
    {

    }
}
