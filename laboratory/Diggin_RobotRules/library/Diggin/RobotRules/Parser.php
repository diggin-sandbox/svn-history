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




class Diggin_RobotRules_Parser
{

    const MULTIBYTESPACE = ' ';


    //protected
    public static function token($robotstxt)
    {
       preg_match_all('!((\A\s*User-agent\s*:.*)+)!si', $robotstxt, $matches);

       return $matches;
    }

    /**
     * parse robots.txt context
     *
     * @param string $robotstxt
     * @return array $rules 
     */
    public static function parse($robotstxt)
    {
        // LINE
        $robotstxt = str_replace(chr(13).chr(10), chr(10), $robotstxt);
        $robotstxt = str_replace(array(chr(10), chr(13), PHP_EOL, $robotstxt));



        $robotstxt = line($robotstxt);

        $robotrules = new Diggin_RobotRules;

        //Diggin_RobotRules_Line::parse($);

        //$splits = preg_split('#User-agent:\s*#si', $robotstxt, -1, PREG_SPLIT_NO_EMPTY);
        //$rules = array();
        //foreach ($splits as $key => $split) {
        //    $line = explode("\n", $split);
        //    $rules[$key]['user_agent'] = trim($line[0]);
        //    if (preg_match_all('#(?:\s|\t)*(Disallow:\s*)(.*)\z#mi', $split, $matches)) {
        //        $rules[$key]['disallow'] = $matches[2];
        //    }
        //}

        return $rules;
    }

    public static function check($robotstxt)
    {

    }
}
