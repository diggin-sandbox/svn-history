<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

/**
 * Helper for Autodiscovery
 *
 * @package    Diggin_Scraper
 * @subpackage Helper
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

require_once 'Diggin/Scraper/Helper/Simplexml/Base.php';

class Diggin_Scraper_Helper_Simplexml_Autodiscovery extends Diggin_Scraper_Helper_Simplexml_Base
{

    /**
     * 
     * <link rel="alternate" type="application/atom+xml" title="Atom" href="http://example.net/atom.xml" />
     * <link rel="alternate" type="application/rss+xml" title="RSS" href="http://example.net/index.rdf" />
     */
    
    const XPATH_BOTH = '//head//link[@rel="alternate" and (@type="application/rss+xml" or @type="application/atom+xml")]//@href';
    const XPATH_RSS = '//head/link[@rel="alternate" and @type="application/rss+xml" and contains(@title, "RSS")]/@href';
    const XPATH_ATOM = '//head/link[@rel="alternate" and @type="application/atom+xml" and contains(@title, "Atom")]/@href';

    /**
     * 
     * 
     * @return mixed
     */
    public function direct()
    {
        $args = func_get_arg(0);
        $type = (isset($args[0]) ? $args[0]: null);
        $baseUrl = (isset($args[1]) ? $args[1] : null);

        return $this->discovery($type, $baseUrl);
    }
    
    public function discovery($type = null, $baseUrl = null)
    {
        if ($type === 'rss') {
            $xpath = self::XPATH_RSS;
        } else if ($type === 'atom') {
            $xpath = self::XPATH_ATOM;
        } else {
            $xpath = self::XPATH_BOTH; 
        }
        
        if ($links = $this->resource->xpath($xpath)) {
            
            $ret = array();
            foreach ($links as $v) {
                
                if (isset($baseUrl)) {
                    $ret[] = Diggin_Uri_Http::getAbsoluteUrl($this->asString($v), $baseUrl);
                } else {
                    $ret[] = $this->asString($v);
                }
            }
            
            return $ret;
        }
        
        return false;
    }
}
