<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Regex extends Diggin_Scraper_Strategy_Abstract 
{

    /**
     * 
     * @param string $resposeBody
     * @return Object SimpleXMLElement
     */
    protected function readData($resposeBody)
    {
        return $resposeBody;
    }
    
    /**
     * 
     * @param string $resposeBody
     * @param string $process
     * @return void
     */
    public function scrape($resposeBody, $process)
    {
        $cleanString = self::_cleanString($resposeBody);
        
        preg_match_all($process, $cleanString , $results);
        
        return $results;
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