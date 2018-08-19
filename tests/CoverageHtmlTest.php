<?php
/**
 * Candybar - Coverage Html Presentation Test
 * @author adrian7
 * @version 1.0
 */

class CoverageHtmlTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\DevLib\Candybar\Coverage\Presentation\Html
     */
    protected static $html = NULL;

    protected function setUp() {

        if( empty(self::$html) )
            self::$html = new \DevLib\Candybar\Coverage\Presentation\Html(
                __DIR__ . '/data/html'
            );

    }

    public function testGetIndex(){

        $path = ( __DIR__ . '/data/html/index.html' );

        $this->assertEquals($path, self::$html->getIndexPath());

    }

    public function testGetCssPath(){

        $path = ( __DIR__ . '/data/html/.css/style.css' );

        $this->assertEquals($path, self::$html->getCssPath( basename($path) ) );

    }

    public function testGetJsPath(){

        $path = ( __DIR__ . '/data/html/.js/script.js' );

        $this->assertEquals($path, self::$html->getJsPath( basename($path) ) );

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testSetStyle(){

        // Style under styles/default.css
        $style = 'default';

        $origin = ( __DIR__ . '/../candybar/styles/default.css' );
        $backup = ( __DIR__ . '/data/html/.css/style.css.bk' );
        $target = ( __DIR__ . '/data/html/.css/style.css' );

        // Backup original file
        copy($target, $backup);

        // Set style
        self::$html->setStyle($style);

        // Did the style was copied?
        $this->assertEquals(
            file_get_contents($origin),
            file_get_contents($target)
        );

        // Restore style
        copy($backup, $target);
        unlink($backup);

    }

    public function testSetupFromFile(){

        $path   = ( __DIR__ . '/data/html/index.html' );
        $object = new \DevLib\Candybar\Coverage\Presentation\Html( $path );

        // Does the path matches?
        $this->assertEquals($path, $object->getIndexPath());

    }

    public function testInvalidSetupFolder(){

        $this->expectException(InvalidArgumentException::class);

        // Does it throws exception?
        $object = new \DevLib\Candybar\Coverage\Presentation\Html( '/whenever' );

        $this->expectException(InvalidArgumentException::class);

        // Does it throws exception?
        $object = new \DevLib\Candybar\Coverage\Presentation\Html(
            __DIR__ . '/data/invalid'
        );

    }

    public function testInvalidSetupCssFolder(){

        $this->expectException(\InvalidArgumentException::class);

        // Does it throws exception?
        $object = new \DevLib\Candybar\Coverage\Presentation\Html(
            __DIR__ . '/data/invalid/missing-css'
        );

    }

    public function testFailsWhenIndexNotReadable() {

        $this->expectException(\InvalidArgumentException::class);

        new \DevLib\Candybar\Coverage\Presentation\Html( __DIR__ . '/data/invalid' );
    }

    public function testFailsWhenStylesFolderNotReadable() {

        $this->expectException(\InvalidArgumentException::class);

        self::$html->addStylesFolder('/path/not/found');

    }

    public function testFailsWhenThemesFolderNotReadable() {

        $this->expectException(\InvalidArgumentException::class);

        self::$html->addThemesFolder('/path/not/found');

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\UnsupportedFeatureException
     */
    public function testUnsupportedFeatureException(){

        $this->expectException(
            \DevLib\Candybar\Exceptions\UnsupportedFeatureException::class
        );

        $path   = ( __DIR__ . '/data/html/index.html' );
        $object = new \DevLib\Candybar\Coverage\Presentation\Html( $path );

        $object->setTheme('missing theme');

    }

}