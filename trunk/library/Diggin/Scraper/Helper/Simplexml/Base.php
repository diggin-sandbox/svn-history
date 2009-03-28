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

require_once 'Diggin/Scraper/Helper/Abstract.php';

abstract class Diggin_Scraper_Helper_Simplexml_Base extends Diggin_Scraper_Helper_Abstract
{
    protected $preAmpFilter = false;
    
    public function setPreAmpFilter($flg)
    {
        $this->preAmpFilter = $flg;
        
        return $this;
    }
    
    protected function asString($sxml)
    {

        if ($this->preAmpFilter === true) {
            if (!is_array($sxml)) {
                return htmlspecialchars_decode($sxml->asXML(),
                            ENT_NOQUOTES);
            } else {
                $ret = array();
                foreach ($sxml as $s) {
                    $ret[] = htmlspecialchars_decode($s->asXML(),
                                ENT_NOQUOTES);
                }
                return $ret;
            }
        } else {
            if (!is_array($sxml)) {
                return $sxml->asXML();
            } else {
                $ret = array();
                foreach ($sxml as $s) {
                    $ret[] = $s->asXML();
                }
                
                return $ret;
            }
        }
    }
}