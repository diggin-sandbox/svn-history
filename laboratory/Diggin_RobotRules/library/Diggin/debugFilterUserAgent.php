<?php
/** Diggin_RobotRules_Protocol_Txt **/
require_once 'RobotRules/Protocol/Txt.php';
/** Diggin_RobotRules_Protocol_Txt_Line **/
require_once 'RobotRules/Protocol/Txt/Line.php';
/** Diggin_RobotRules_Protocol_Txt_Record **/
require_once 'RobotRules/Protocol/Txt/Record.php';

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

$protocol = new Diggin_RobotRules_Protocol_Txt($robots);

class Diggin_RobotRules_Accept_Txt_UserAgentSearchFilter extends FilterIterator
{

    private $_useragent = "";
    
    public function __construct(Diggin_RobotRules_Protocol_Txt $protocol, $useragent = '*')
    {
        parent::__construct($protocol);
        $this->_useragent = $useragent;   
    }

    public function accept()
    {
        $record = $this->current();

       //todoo handle if none user-agent
       //if (!array_key_exists('User-Agent', $record))
        foreach ($record['user-agent'] as $ua)
        {
            if ($this->_useragent === $ua->getValue() or
                '*' === $ua->getValue()) {
                return true;
            }
        }

        return false;
    }
}


foreach (new Diggin_RobotRules_Accept_Txt_UserAgentSearchFilter($protocol, 'Google') as $key => $record)
{
    var_dump($record);
}
