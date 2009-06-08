<?php

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
