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

abstract class Diggin_Scraper_Helper_HelperAbstract
{
    /**
     * @var mixed $_resource
     */
    private $_resource;

    /**
     * Constructor
     *
     * Add resource
     *
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->_resource = $resource;
    }


    abstract public function direct();

    /**
     * get resource for scraping
     *
     * @return mixed $_resource
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * magic method _invoke for using over PHP5.3
     * call only direct() method
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->direct();
    }
}
