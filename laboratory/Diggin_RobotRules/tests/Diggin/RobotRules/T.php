<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Version.php';

/**
 * Test class for Diggin_Version.
 * Generated by PHPUnit on 2009-02-23 at 21:52:40.
 */
class Diggin_RobotRules_VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Version
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
        $this->object = new Diggin_Version;
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
     * @todo Implement testCompareVersion().
     */
    public function testCompareVersion() {
        $expect = -1;
        
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//
        
        for ($i=0; $i <= 1; $i++) {
            for ($j=0; $j < 10; $j++) {
                for ($k=0; $k < 20; $k++) {
                    foreach (array('PR', 'dev', 'alpha', 'beta', 'RC', 'RC1', 'RC2', 'RC3', '', 'pl') as $rel) {
                        $ver = "$i.$j.$k$rel";
                        if ($ver === Diggin_Version::VERSION
                            || "$i.$j.$k-$rel" === Diggin_Version::VERSION
                            || "$i.$j.$k.$rel" === Diggin_Version::VERSION
                            || "$i.$j.$k $rel" === Diggin_Version::VERSION) {

                            if ($expect != -1) {
                                $this->fail("Unexpected double match for Diggin_Version::VERSION ("
                                    . Diggin_Version::VERSION . ")");
                            }
                            else {
                                $expect = 1;
                            }
                        } else {
                            $this->assertSame(Diggin_Version::compareVersion($ver), $expect,
                                "For version '$ver' and Diggin_Version::VERSION = '"
                                . Diggin_Version::VERSION . "': result=" . (Diggin_Version::compareVersion($ver))
                                . ', but expected ' . $expect);
                        }
                    }
                }
            }
        }
        
        if ($expect === -1) {
            $this->fail('Unable to recognize Diggin_Version::VERSION ('. Diggin_Version::VERSION . ')');
        }

    }
}
?>
