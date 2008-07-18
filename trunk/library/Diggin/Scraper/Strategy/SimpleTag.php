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
            require_once 'Diggin/Scraper/Adapter/Normal.php';
            self::$_adapter = new Diggin_Scraper_Adapter_Normal();
        }

        return self::$_adapter;
    }

    public function scrape($respose, $process)
    {
        $adptBody = $this->getAdapter()->readData($respose);
        
        return self::extract($adptBody, $process);
    }
    
    public static function extract($values, $process)
    {
        
        if(!is_string($values)) $values = $values->plain;
        
        SimpleTag::setof($markup, $values);
        $results = $markup->getIn($process->expression);
        return $results;
    }
    
    /**
     * get value with DSL
     * 
     * @param array
     * @param Diggin_Scraper_Process
     * @return array
     */
    public function getValue($values, $process)
    {
        if (strtoupper(($process->type)) === 'RAW') {
            $strings = $values;
        } elseif (strtoupper(($process->type)) === 'TEXT') {
            $strings = array();
            foreach($values as $value) {
                $strings[] = $value->value;
            }
        } elseif (strpos($process->type, '@') === 0) {
            $strings = array();
            foreach ($values as $value) {
                foreach($value->parameterList as $parameter) {
                    if($parameter->id == substr($process->type, 1)) {
                        $strings[] = $parameter->value;
                    }
                }
            }
        } else {
            require_once 'Diggin/Scraper/Strategy/Exception.php';
            throw new Diggin_Scraper_Strategy_Exception("can not understand type :".$process->type);
        }
        
        return $strings;
    }
}
