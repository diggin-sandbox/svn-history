<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Scraper/Process.php';

/**
 * Test class for Diggin_Scraper_Process.
 * Generated by PHPUnit on 2008-10-05 at 01:46:53.
 */
class Diggin_Scraper_ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Scraper_Process
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Diggin_Scraper_Process;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement test__toString().
     */
    public function test__toString() {
        
        $this->object->setExpression($exp = '//expression');
        $this->object->setName($key ='key[]');
        $this->object->setType($val = 'val');
        $this->object->setFilters($filter = array('filter'));
        
  //      $filter = implode(', ', $filter);
        $process = "'$exp', "."'$key => [\"$val\", \"$filter[0]\"]'";

        
        $this->assertEquals($process, (string) $this->object);
        
        $this->object->setFilters($filter = array('filter', 'filter2'));
        
        $process = "'$exp', "."'$key => [\"$val\", \"$filter[0], $filter[1]\"]'";
        
        $this->assertEquals($process, (string) $this->object);
                $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddProcess().
     */
    public function testAddProcess() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
