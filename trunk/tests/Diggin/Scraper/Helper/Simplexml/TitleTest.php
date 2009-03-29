<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Scraper/Helper/Simplexml/Title.php';

/**
 * Test class for Diggin_Scraper_Helper_Simplexml_Title.
 * Generated by PHPUnit on 2009-03-27 at 23:11:39.
 */
class Diggin_Scraper_Helper_Simplexml_TitleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Scraper_Helper_Simplexml_Title
     * @access protected
     */
    protected $object;
    protected $object1;
    protected $responseBody;
    protected $responseBody1;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        
        $this->responseBody = $responseBody = '<html lang="ja">'.PHP_EOL.
                           '<head>'.PHP_EOL.
                           "<title>
                           
                            <b>f&lt;i>rs&amp;t</b>
                           
                           
                           
                           </title>".
                           '<title>
                           
                           second   </title>'.
                           '</head>'.
                           '<body>'.PHP_EOL.
                           '<a href="test/test.html">testlink</a><br /><br />'.PHP_EOL.
                           '</body>'.PHP_EOL.
                           '</html>';
        //$simplexml = simplexml_load_string($responseBodyWithHeadTag1);
        
        //@todo
//        $simplexml = Diggin_Scrpaer_Adapter_Htmlscraping($responseBodyWithHeadTag1);
        
        $responseBody2 = str_replace('&', '&amp;', $responseBody);
         $simplexml = simplexml_load_string($responseBody2);
         $this->object = new Diggin_Scraper_Helper_Simplexml_Title($simplexml);
        $this->object->setPreAmpFilter(true);
        
        $simplexml = simplexml_load_string($responseBody);
         $this->object1 = new Diggin_Scraper_Helper_Simplexml_Title($simplexml);
        
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
     * @todo Implement testDirect().
     */
    public function testDirect() {
        $expect = '<b>f<i>rs&t</b>';
        
//        $s = new Diggin_Scraper();
//        $scrap = $s->process('//title', 'title => DISP')
//          ->scrape(array($this->responseBody));
//        print_r($scrap);

        $this->assertEquals($expect,$this->object->direct());
 
        $this->assertEquals($expect,$this->object1->direct());
        
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
