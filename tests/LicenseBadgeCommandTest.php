<?php
/**
 * Candybar - Test of LicenseBadgeCommand class
 * @author adrian7
 * @version 1.0
 */

class LicenseBadgeCommandTest extends CliCommandTest {

    public function testCommand(){

        $filename = ( __DIR__ . '/data/license-badge-test.svg' );
        $color    = '0B9BA9';
        $lic      = 'BSD2';

        $this->silent('license:badge', [
            $filename,
            $lic,
            "--color={$color}"
        ]);

        //Does the file was saved?
        $this->assertFileExists($filename);

        //Does the file has expected text?
        $this->assertContains($lic, file_get_contents($filename));

        //Does the file has expected color?
        $this->assertContains($color, file_get_contents($filename));

        //Cleanup
        @unlink($filename);

    }

    public function testThrowsException(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        //Run the command
        $this->verbose( 'license:badge', [
            '--style=unknown'
        ]);

    }

}