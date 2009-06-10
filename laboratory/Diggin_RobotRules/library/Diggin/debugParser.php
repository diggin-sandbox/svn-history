<?php
/** Diggin_RobotRules_Line **/
require_once 'Line.php';
/** Diggin_RobotRules_Record **/
require_once 'Record.php';
/** Diggin_RobotRules_Parser **/
require_once 'Parser.php';

if (debug_backtrace()) return;

$robots = <<<EOF
User-agent: Google
User-agent: Infoseek
Disallow:

User-Agent: Googlebot
Disallow: /cgi-bin/
Disallow: /*.gif$

User-agent: *
Disallow: /     
EOF;

$robots = Diggin_RobotRules_Parser::parse($robots);
//$robots = new Diggin_RobotRules();
//$robots->set($parsed);
var_dump($robots->current());
var_dump($robots->next());
var_dump($robots->current());


