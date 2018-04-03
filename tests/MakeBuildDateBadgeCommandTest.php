<?php
/**
 * Candybar - Test for AddBadgesToReadmeCommand class
 * @author adrian7
 * @version 1.0
 */

class MakeBuildDateBadgeCommandTest extends CliCommandTest {


    public function testCommand(){

        $filename = ( __DIR__ . '/data/builddate-badge.svg' );
        $color    = '0A0A0A';

        $timezone = new DateTimeZone('UTC');
        $format   = 'Y-m-d H:i:s';

        $this->silent('build:badge:date', [
            $filename ,
            "--color={$color}",
            "--timezone=UTC",
            "--format={$format}"
        ]);

        //Did the file was saved?
        $this->assertFileExists($filename);

        //Did the badge has expected values?
        $this->assertContains($color, @file_get_contents($filename));
        $this->assertContains(
            \Carbon\Carbon::now($timezone)->format($format),
            @file_get_contents($filename)
        );

        //Cleanup
        @unlink($filename);

    }

}