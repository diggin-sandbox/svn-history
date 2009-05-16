<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Framework/Provider/Abstract.php';


class Gethna_Context_Gethna
    //extends Zend_Tool_Framework_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Interface
{
    public function say()
    {
           echo 'Hellllllllllllllllllo from my provider!';
    }


    public function foo()
    {
        echo __LINE__;
    }
}

