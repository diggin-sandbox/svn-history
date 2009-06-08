<?php

class Diggin_RobotRules_Parser
{
    

    public static function parse($robotstxt)
    {
        //if (is_string())
        //preg_match('/^User-agent:(?P<useragent>.*?)Disallow:(.*)/si', $robotstxt, $matches);
        
        $splits = preg_split('#User-agent:\s*#si', $robotstxt, -1, PREG_SPLIT_NO_EMPTY);

        $rules = array();
        foreach ($splits as $key => $split) {
            if (preg_match('#^(?:\t|\s)*(?<!Disallow:)(?:.*)$#mi', $split, $matches)) {
                $rules[$key]['user-agent'] = $matches[0];
            } else 
                //if (preg_match('#^\s*Disallow:\s*(.*)$#mi', $split, $matches)){
            {
                $rules[$key]['disallow'] = $matches;
                var_dump($matches);
                //$rules[$key]['disallow'] = $matches;
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
