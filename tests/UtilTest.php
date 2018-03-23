<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class UtilTest extends \PHPUnit\Framework\TestCase{

    public function testParseLoggingConfig(){

        $path = ( __DIR__ . '/data/phpunit-sample.xml' );

        $all = \DevLib\Candybar\Util::getLoggingConfig($path);
        $one = \DevLib\Candybar\Util::getLoggingConfig($path, 'coverage-text');

        $this->assertTrue( is_array($all) and isset($all[0]) );
        $this->assertArrayHasKey('target', $all[0]);

        $this->assertArrayHasKey('target', $one);
        $this->assertEquals('php://stdout', $one['target']);

    }

    public function testParseCloverMetrics(){

        $path = ( __DIR__ . '/data/coverage-clover-sample.xml' );

        $all = \DevLib\Candybar\Util::getCloverXmlMetrics($path);
        $one = \DevLib\Candybar\Util::getCloverXmlMetrics($path, 'coveredstatements');

        $this->assertTrue( is_array($all) and isset($all['loc']) );
        $this->assertEquals(2253, $all['loc']);

        $this->assertEquals(60, $one);

    }

    public function testLookupFile(){

        //Relative path
        $name = 'phpunit-sample';
        $dir  = join(DIRECTORY_SEPARATOR, ['tests', 'data']);
        $ext  = 'xml';

        $this->assertEquals(
            realpath($dir . DIRECTORY_SEPARATOR . $name . ".{$ext}"),
            \DevLib\Candybar\Util::lookupFile($name, $ext, [$dir])
        );

        //In cwd
        $cwd = getcwd();
        chdir( realpath($dir) );

        $this->assertEquals(
            realpath($name . ".{$ext}"),
            \DevLib\Candybar\Util::lookupFile($name, $ext, [$dir])
        );

        //Restore cwd
        chdir($cwd);

    }
}