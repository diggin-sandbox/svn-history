<?php
require_once 'Diggin/Scraper/Strategy/Abstract.php';

class Diggin_Scraper_Strategy_Loadhtml extends Diggin_Scraper_Strategy_Abstract
{
    protected $config = array(
        'xml_manifesto'         => true,
    );
    
    /**
     * 
     * @return 
     */
    protected function readData($body)
    {
        $dom = @DOMDocument::loadHTML($body);
        $simplexml = simplexml_import_dom($dom);
        
    	// ここまででもいいのだけど。
    	// XML宣言が付いていないので付与する。
    	if ($this->config["xml_manifesto"] === true) {
    		$str = $simplexml->asXML();
    		{
    			// XML宣言付与
    			if (1 !== preg_match('/^<\\?xml version="1.0"/', $str)) {
    				$str = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $str;
    			}
    			
    			//@see http://goungoun.dip.jp/app/fswiki/wiki.cgi/devnotebook?page=PHP5%A1%A2%CC%A4%C0%B0%B7%C1HTML%A4%F2SimpleXML%A4%D8%CA%D1%B4%B9
    			// HTML中の改行が数値文字参照になってしまったので、
    			// 文字に戻す。
    			$str = $this->numentToChar($str);
    		}
    		$simplexml = simplexml_load_string($str);
    	}
        
        return $simplexml;
    }
    
    public function setConfig($config = array())
    {
        if (! is_array($config))
            throw new Diggin_Scraper_Strategy_Exception('Expected array parameter, given ' . gettype($config));

        foreach ($config as $k => $v)
            $this->config[strtolower($k)] = $v;

        return $this;
    }
    
    /**
     * 数値文字参照を文字に戻す。
     *
     * 以下より
     * http://blog.koshigoe.jp/archives/2007/04/phpdomdocument.html
     * 
     * @param string $string
     * @return string
     */
    private static function numentToChar($string)
    {
        $excluded_hex = $string;
        if (preg_match("/&#[xX][0-9a-zA-Z]{2,8};/", $string)) {
            // 16 進数表現は 10 進数に変換
            $excluded_hex = preg_replace("/&#[xX]([0-9a-zA-Z]{2,8});/e",
                                         "'&#'.hexdec('$1').';'", $string);
        }
        return mb_decode_numericentity($excluded_hex,
                                       array(0x0, 0x10000, 0, 0xfffff),
                                       "UTF-8");
    }
}