<?php


require_once 'Zend/Tool/Framework/Registry.php';
require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Version.php';

/**
 *  * Version Provider
 *   *
 *    */
class Gethna_Provider_Geth
    implements Zend_Tool_Framework_Provider_Interface, Zend_Tool_Framework_Registry_EnabledInterface
{

        /**
         *      * @var Zend_Tool_Framework_Registry_Interface
         *           */
        protected $_registry = null;
            

                        protected $_specialties = array('MajorPart', 'MinorPart', 'MiniPart');

                            public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
                                    {  
                                                $this->_registry = $registry;
                                                        return $this;
                                                           }
    public function gethAction()
    {
        echo __LINE__;
    }
}

