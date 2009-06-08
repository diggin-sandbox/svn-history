<?php
/** Diggin_RobotRules_Line **/
require_once 'Line.php';
/** Diggin_RobotRules_Record **/
class Diggin_RobotRules_Record implements ArrayAccess
{
    private $_lines = array();

    public function offsetSet($offset, $value) 
    {
        //$count = isset($this->_lines[$offset]) ? count($this->_lines[$offset]) : 0;
        $this->_lines[$offset] = $value;
    }
    public function offsetExists($offset)
    {
        return isset($this->_lines[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->_lines[$offset]);
    }
    public function offsetGet($offset)
    {
        //$count = isset($this->_lines[$offset]) ? count($this->_lines[$offset]) : 0;
        return isset($this->_lines[$offset]) ? $this->_lines[$offset] : '';
    }

    public function __toString()
    {
        return implode("\n", $this->_lines);
    }
}

$line = new Diggin_RobotRules_Line();
$line->setField('User-Agent');
$line->setValue('Mozilla');

$record = new Diggin_RobotRules_Record();
$record['agents'] = $line;

var_dump($record);
echo $record;

return;

class Diggin_RobotRules_Parser
{

    /**
     * parse robots.txt context
     *
     * @param string $robotstxt
     * @return array $rules 
     */
    public static function parse($robotstxt)
    {
        $splits = preg_split('#User-agent:\s*#si', $robotstxt, -1, PREG_SPLIT_NO_EMPTY);

        $rules = array();
        foreach ($splits as $key => $split) {
            $line = explode("\n", $split);
            $rules[$key]['user_agent'] = trim($line[0]);
            if (preg_match_all('#(?:\s|\t)*(Disallow:\s*)(.*)\z#mi', $split, $matches)) {
                $rules[$key]['disallow'] = $matches[2];
            }
        }

        return $rules;
    }
}

if (debug_backtrace()) return;

$robots = <<<EOF
User-agent: Google
Disallow:

User-Agent: Googlebot
Disallow: /cgi-bin/
Disallow: /*.gif$

User-agent: *
Disallow: /     
EOF;

$parsed = Diggin_RobotRules_Parser::parse($robots);
var_dump(Diggin_RobotRules_Parser::parse($robots));

