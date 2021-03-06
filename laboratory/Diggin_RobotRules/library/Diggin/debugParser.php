<?php
/** Diggin_RobotRules_Protocol_Txt **/
require_once 'RobotRules/Protocol/Txt.php';
/** Diggin_RobotRules_Protocol_Txt_Line **/
require_once 'RobotRules/Protocol/Txt/Line.php';
/** Diggin_RobotRules_Protocol_Txt_Record **/
require_once 'RobotRules/Protocol/Txt/Record.php';

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

//$robots = "";


$robots = new Diggin_RobotRules_Protocol_Txt($robots);

//var_dump($robots);exit;

foreach ($robots as $key => $record)
{
    //var_dump($key, $record);
    var_dump($record['user-agent']);
}
//var_dump($robots->current());
//var_dump($robots->next());
//var_dump($robots->current());


