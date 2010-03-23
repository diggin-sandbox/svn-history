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
 * @copyright  2006-2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

class Diggin_Scraper_Callback_Evaluator extends ArrayIterator
{
    /**
     * Process
     *
     * @var Diggin_Scraper_Process
     */
    private $_process;

    /**
     * Evaluator
     *
     * @var Diggin_Scraper_Evaluator_Abstract
     */
    private $_evaluator;

    /**
     * constructor
     *
     * @param array $values
     * @param Diggin_Scraper_Process $process
     * @param Diggin_Scraper_Evaluator_Abstract $evaluator
     */
    public function __construct(array $values, 
                                Diggin_Scraper_Process $process, 
                                Diggin_Scraper_Evaluator_Abstract $evaluator)
    {
        $this->_process = $process;
        $this->_evaluator = $evaluator;

        return parent::__construct($values);
    }

    /**
     * Call evaluator's method
     * override parent ArrayIterator's current()
     * 
     * @return mixed
     */
    public function current()
    {
        return call_user_func(array($this->_evaluator, $this->_process->getType()), parent::current());
    }

    /**
     * Get process
     *
     * @return Diggin_Scraper_Process
     */
    public function getProcess()
    {
        return $this->_process;
    }
}
