<?php
/**
 * Candybar - Util class test
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

        $path = ( __DIR__ . '/data/coverage/clover-sample.xml' );

        $all = \DevLib\Candybar\Util::getCloverXmlMetrics($path);
        $one = \DevLib\Candybar\Util::getCloverXmlMetrics($path, 'coveredstatements');

        $this->assertTrue( is_array($all) and isset($all['loc']) );
        $this->assertEquals(2253, $all['loc']);

        $this->assertEquals(60, $one);

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
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

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testFindFileOrFail(){

        //Relative path
        $name = 'phpunit-sample';
        $dir  = join(DIRECTORY_SEPARATOR, ['tests', 'data']);
        $ext  = 'xml';

        $this->assertEquals(
            realpath($dir . DIRECTORY_SEPARATOR . $name . ".{$ext}"),
            \DevLib\Candybar\Util::findFileOrFail($name, [$dir], $ext)
        );

        //Test if it throws exception
        try{

            $name = 'phpunit-missing-sample';

            $path =
                \DevLib\Candybar\Util::findFileOrFail($name, [$dir], $ext);

        }
        catch (\DevLib\Candybar\Exceptions\UnreadableFileException $e){
            //Caught exception test passed
            $this->assertTrue(TRUE);
        }

    }

    public function testCopyDir(){

        $source = (__DIR__ . '/data/html');
        $dest   = (__DIR__ . '/data/html-copied');

        $testFileOne = ('index.html');
        $testFileTwo = ('.css/style.css');

        //Copy folder
        \DevLib\Candybar\Util::copyDir($source, $dest);

        //Did test files got copied?
        $this->assertFileExists(
            $dest . DIRECTORY_SEPARATOR . $testFileOne
        );

        $this->assertFileExists(
            $dest . DIRECTORY_SEPARATOR . $testFileTwo
        );

        //Do file contents matches
        $this->assertEquals(
            file_get_contents($source . DIRECTORY_SEPARATOR . $testFileOne),
            file_get_contents($dest . DIRECTORY_SEPARATOR . $testFileOne)
        );

        $this->assertEquals(
            file_get_contents($source . DIRECTORY_SEPARATOR . $testFileTwo),
            file_get_contents($dest . DIRECTORY_SEPARATOR . $testFileTwo)
        );

        //Cleanup
        $sys = new \Symfony\Component\Filesystem\Filesystem();
        $sys->remove($dest);

    }

    public function testGetSize(){

        $file   = ( __DIR__ . '/data/folder/1024bytes' );
        $folder = ( __DIR__ . '/data/folder' );

        //Does the size matches
        $this->assertEquals(1024, \DevLib\Candybar\Util::getSize($file));
        $this->assertEquals(1024*2, \DevLib\Candybar\Util::getSize($folder));

    }

    public function testRound(){

        $this->assertEquals(
            12.861,
            \DevLib\Candybar\Util::round(12.86056, 3)
        );

        $this->assertEquals(
            9.77,
            \DevLib\Candybar\Util::round(9.76537, 2)
        );

        $this->assertEquals(
            53.1,
            \DevLib\Candybar\Util::round(53.1234, 1)
        );

        $this->assertEquals(
            10,
            \DevLib\Candybar\Util::round(10.2525, 0)
        );

    }

    public function testGetSizeHuman(){

        $file   = ( __DIR__ . '/data/folder/1024bytes' );
        $folder = ( __DIR__ . '/data/folder' );

        $this->assertEquals('1.0K', \DevLib\Candybar\Util::getSizeHuman($file));
        $this->assertEquals('2.0K', \DevLib\Candybar\Util::getSizeHuman($folder));

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testFalseWhenFileNotFound(){

        $result = \DevLib\Candybar\Util::lookupFile(
            '/path/to/somefile',
            '.ext',
            ['/there']
        );

        $this->assertFalse($result);

    }

    public function testFailsToReadMissingXml(){

        $this->expectException(\InvalidArgumentException::class);

        \DevLib\Candybar\Util::parseXml('/very/obnoxious/path.xml', []);

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testFailsOnInvalidLookupPaths(){

        $this->expectException(\InvalidArgumentException::class);

        \DevLib\Candybar\Util::lookupFile('somefile', '', [new stdClass()]);

    }

}