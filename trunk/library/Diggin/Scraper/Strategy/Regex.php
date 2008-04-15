<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Regex extends Diggin_Scraper_Strategy_Abstract 
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

    /**
     * 
     * @param string $respose
     * @param string $process
     * @return void
     */
    public function scrape($respose, $process)
    {
        $adapterBody = $this->getAdapter()->readData($respose);
        
        $cleanString = self::_cleanString($adapterBody);
        
        preg_match_all($process->expression, $cleanString , $results);
        
        return $results;
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
    
    /**
     * オライリーのスパイダリング本から
     * 
     * @param string
     * @results string
     */
    private static function _cleanString($resposeBody){
        $results = str_replace(array(chr(10), chr(13), chr(9)), chr(32), $resposeBody);
    	while(strpos($results, str_repeat(chr(32), 2), 0) != FALSE){
    	    $results = str_replace(str_repeat(chr(32), 2), chr(32), $results);
    	}
    	return (trim($results));
    }
}
