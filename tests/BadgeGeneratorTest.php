<?php
/**
 * Candybar - BadgeGenerator class test
 * @author adrian7
 * @version 1.0
 */

use \DevLib\Candybar\Graphics\BadgeGenerator;

class BadgeGeneratorTest extends \PHPUnit\Framework\TestCase{

    public function testMake(){

        $color = '92B4F4';
        $text  = str_random(6);
        $value = strtoupper( str_random(3) );

        foreach ([
            BadgeGenerator::STYLE_FLAT,
            BadgeGenerator::STYLE_FLAT_SQUARE,
            BadgeGenerator::STYLE_PLASTIC
        ] as $style){

            //Generate badge
            $badge = BadgeGenerator::make(
                $text,
                $value,
                $color,
                $style
            );

            //Did wwe got the expected object?
            $this->assertInstanceOf(BadgeGenerator::class, $badge);

            //Does the svg has our params?
            $this->assertContains($color, $badge->getSvg());
            $this->assertContains($text, $badge->getSvg());
            $this->assertContains($value, $badge->getSvg());

        }

    }

    public function testSave(){

        $text     = str_random(6);
        $filename = ( __DIR__ . '/data/test-badge.svg' );

        //Generate badge
        $badge = \DevLib\Candybar\Graphics\BadgeGenerator::make(
            $text,
            'test'
        );

        //Save badge
        $bytes = $badge->save($filename);

        //Did the file got written?
        $this->assertTrue(0 < $bytes);

        //Did the file has text?
        $this->assertFileExists($filename);
        $this->assertContains($text, @file_get_contents($filename));

        //Cleanup
        @unlink($filename);

    }

    public function testErrorWhenEmptyFilename(){

        $this->expectException(InvalidArgumentException::class);

        //Did we get an error when we miss the filename?
        \DevLib\Candybar\Graphics\BadgeGenerator::make(
            'subject',
            'value'
        )->save('');

    }

    public function testErrorWhenFilenameIsNotSvg(){

        $this->expectException(InvalidArgumentException::class);

        //Did we get an error when we miss the filename?
        \DevLib\Candybar\Graphics\BadgeGenerator::make(
            'subject',
            'value'
        )->save('somefile.not');

    }

}