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
class Diggin_Scraper_Process
{
    public $expression;
    public $name;
    public $arrayflag;
    public $type;
    public $filters;
    
    public $processes;
    
    public function __toString()
    {
        return '\''.$this->expression.'\' , '.
               $this->name.' => '. $this->type. '"';
    }
    
    public function __construct($expression = null, $name = null, $arrayflag = false, $type = null, $filters = false)
    {
        if (strtolower(($name)) === 'results') {
            require_once 'Diggin/Scraper/Exception.php';
            throw new Diggin_Scraper_Exception('key "results" is not allowed');
        }
        
        $this->expression = $expression;
        $this->name = $name;
        $this->arrayflag = $arrayflag;
        $this->type = $type;
        $this->filters = $filters;
    }
    
    public function addProcess($args)
    {
        $args = func_get_args();
        
        if(count($args) === 1) {
            $this->processes = $args;            
        }
        $expression = array_shift($args);
        $namestypes = $args;

        foreach ($namestypes as $nametype) {
            if(is_string($nametype)) {
                //$types = null;
                if (strpos($nametype, '=>') !== false) list($name, $types) = explode('=>', $nametype);
                if (!isset($types)) $name = $nametype;
                if ((substr(trim($name), -2) == '[]')) {
                    $name = substr(trim($name), 0, -2);
                    $arrayflag = true;
                } else {
                    $arrayflag = false;
                }
                if (!isset($types)) {
                    $this->processes[] = new Diggin_Scraper_Process($expression, trim($nametype), $arrayflag);
                } else {
                    $types = trim($types, " '\"");
                    if (strpos($types, ',') !== false) $types = explode(',', $types);
                    if (count($types) === 1) {
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag, $types);
                    } else {
                        foreach ($types as $count => $type) {
                            if ($count !== 0) $filters[] = trim($type, " []'\"");
                        }
                        $this->processes[] = 
                        new Diggin_Scraper_Process($expression, trim($name), $arrayflag,
                                                   trim($types[0], " []'\""), $filters);
                    }
                }
            } elseif (is_array($nametype)) {
                if(!is_numeric(key($nametype))) {
                    if ((substr(key($nametype), -2) == '[]')) {
                        $name = substr(key($nametype), 0, -2);
                        $arrayflag = true;
                    } else {
                        $name = key($nametype);
                        $arrayflag = false;
                    }
                    $this->processes[] = new Diggin_Scraper_Process($expression, $name, $arrayflag, array_shift($nametype));
                } else {
                    $this->processes[] = new Diggin_Scraper_Process($expression, $nametype[0], $nametype[1], $nametype[2], $nametype[3]);
                }
            }
        }
        
        return $this;
    }
}
