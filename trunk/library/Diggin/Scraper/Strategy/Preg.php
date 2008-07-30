<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
require_once 'Diggin/Scraper/Strategy/Abstract.php';
class Diggin_Scraper_Strategy_Preg extends Diggin_Scraper_Strategy_Abstract 
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

    /**
     * 
     * @param string $respose
     * @param string $process
     * @return array
     */
    public function scrape($respose, $process)
    {
        $adapterBody = $this->getAdapter()->readData($respose);

        $cleanString = self::cleanString($adapterBody);
        
        return self::extract($cleanString, $process);
    }
    
    public static function extract($cleanString, $process)
    {
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
    public function getValue($values, $process)
    {
        return $values;
    }
    
    /**
     * オライリーのスパイダリング本から
     * 
     * @param string
     * @results string
     */
    private static function cleanString($resposeBody)
    {
        $results = str_replace(array(chr(10), chr(13), chr(9)), chr(32), $resposeBody);
        while(strpos($results, str_repeat(chr(32), 2), 0) != FALSE){
            $results = str_replace(str_repeat(chr(32), 2), chr(32), $results);
        }

        return (trim($results));
    }
}