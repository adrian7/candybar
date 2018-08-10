<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

class CoverageHtmlStyleCommandTest extends CliCommandTest {

    public function testCommand(){

        $root     = ( __DIR__ . '/data/html' );
        $original = ( __DIR__ . '/data/html/.css/style.css' );
        $backup   = ( __DIR__ . '/data/html/.css/style.css.bk' );

        //Backup original file
        if( copy($original, $backup) );
        else
            $this->fail("Could not copy {$original} to {$backup} ... .");

        $this->silent(
            'coverage:style',
            [ "--root={$root}" ]
        );

        //Does file exists?
        $this->assertNotEquals(
            file_get_contents($backup),
            file_get_contents($original)
        );

        //Restore backup
        copy($backup, $original);

        //Cleanup
        @unlink($backup);

    }

    public function testThrowsException(){

        // Expects exception to be thrown
        $this->expectException(\DevLib\Candybar\Exceptions\UnreadableFileException::class);

        chdir(__DIR__ . '/data/invalid-phpunit-config');

        // Run the command
        $this->silent( 'coverage:style');

    }

}