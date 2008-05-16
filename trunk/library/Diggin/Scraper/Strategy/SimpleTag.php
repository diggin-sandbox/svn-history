<?php
require_once 'rhaco/Rhaco.php';
Rhaco::import("tag.model.SimpleTag");
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_SimpleTag extends Diggin_Scraper_Strategy_Abstract
{
    protected static $_adapter = null;
    
    public function __destruct() {
       self::$_adapter = null;
       parent::$_adapter = null;
   }
    
    public function setAdapter(Diggin_Scraper_Adapter_Interface $adapter)
    {
        self::$_adapter = $adapter;
    }

    public function getAdapter()
    {
        if(isset(self::$_adapter)){
            return self::$_adapter;
        }
        
        //コンストラクタで設定されてた時用
        if (parent::$_adapter instanceof Diggin_Scraper_Adapter_Interface) {
            return parent::$_adapter;
        } else {
            /**
             * @see Diggin_Scraper_Adapter
             */
            require_once 'Diggin/Scraper/Adapter/Raw.php';
            self::$_adapter = new Diggin_Scraper_Adapter_Raw();
        }

        return self::$_adapter;
    }

    public function scrape($respose, $process)
    {
        $adptBody = $this->getAdapter()->readData($respose);
        
        SimpleTag::setof($markup, $adptBody);
        $result = $markup->getIn($process->expression);
        
        return $result;
    }
    
	/**
     * get value with DSL
     * 
     * @param Diggin_Scraper_Context
     * @param Diggin_Scraper_Process
     * @return array
     */
    public function getValue($context, $process)
    {
        return $context->scrape($process);
    }
}
