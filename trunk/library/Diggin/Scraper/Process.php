<?php
/**
 * Diggin - Library Of PHP
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
    public $type;
    public $filters;

    public function __construct($expression, $name, $type = null, $filters = false)
    {
        $this->expression = $expression;
        $this->name = $name;
        $this->type = $type;
        $this->filters = $filters;
    }
}