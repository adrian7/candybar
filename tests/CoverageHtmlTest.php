<?php
/**
 * PHPUnit Coverage Styles - [file description]
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

        $this->assertEquals($path, self::$html->getCssPath( basename($path) ) );

    }

    public function testSetStyle(){

        //Style under styles/default.css
        $style = 'default';
        $target= ( __DIR__ . '/data/html/.css/style.css' );

        self::$html->setStyle($style);

    }
}