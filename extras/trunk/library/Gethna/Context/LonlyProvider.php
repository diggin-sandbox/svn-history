<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Framework/Provider/Abstract.php';


class Gethna_Context_LonlyProvider
    //extends Zend_Tool_Framework_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Interface
{
    public function say($namae = 'test', $userplace = 'shibuya')
    {
           echo "$namae is lonly @ $userplace...";
    }


    public function foo()
    {
        echo __LINE__;
    }
}

