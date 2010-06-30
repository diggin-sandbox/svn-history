<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * Test class for Diggin_Scraper.
 * Generated by PHPUnit on 2009-01-16 at 22:23:44.
 */
class Diggin_Scraper_ResourceHandleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Scraper
     * @access protected
     */
    protected $object;

    private $_responseHeader200 = "HTTP/1.1 200 OK\r\nContent-type: text/html";
    
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Diggin_Scraper;
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

    public function testGetResponse()
    {
        $expected = 'さかーにゅーす';
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>$expected</title>
<body>
bb
</body>
</html>
HTML;

        $this->object->process('//title', 'keyww', 'TEXT');
        $results = $this->object->scrape(array($html));
        
        $this->assertEquals($expected, $results['keyww']);
        
        $expected2 = 'title';
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>$expected2</title>
<body>
aa
</body>
</html>
HTML;
        $this->object = new Diggin_Scraper;
        $this->object->process('//title', 'key', 'TEXT');
        $results = $this->object->scrape(array($html));
        $this->assertEquals($expected2, $results['key']);
    }
}
?>
