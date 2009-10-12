<?php

class Diggin_RobotRules_Accepter_Txt implements Diggin_RobotRules_Accepter_AccepterInterface
{
    private $_protocol;

    public function isAllowed()
    {
    }

    public function setProtocol($protocol)
    {
        if (!($protocol instanceof Diggin_RobotRules_Protocol_Txt)) {
            throw new Exception();
        }


    }

}
