<?php
/** Diggin_RobotRules_Line **/
require_once 'Line.php';
/** Diggin_RobotRules_Record **/
require_once 'Record.php';

$line1 = new Diggin_RobotRules_Line();
$line1->setField('Disallow');
$line1->setValue('/');
$line2 = new Diggin_RobotRules_Line();
$line2->setField('User-Agent');
$line2->setValue('Mozilla');
$line3 = new Diggin_RobotRules_Line();
$line3->setField('Allow');
$line3->setValue('/test');

$record = new Diggin_RobotRules_Record();
$record->add($line1);
$record->add($line2);
$record->add($line3);

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

