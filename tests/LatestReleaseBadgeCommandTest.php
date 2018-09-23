<?php
/**
 * Candybar - Test for LatestReleaseBadgeCommand
 * @author adrian7
 * @version 1.0
 */

class LatestReleaseBadgeCommandTest extends CliCommandTest {

    public function testCommand(){

        $filename  = ( __DIR__ . '/data/release-badge.svg' );
        $string    = (
            'v' . \DevLib\Candybar\Cli::VERSION .
            ' (' . \DevLib\Candybar\Cli::CODENAME . ')'
        );

        $this->silent( 'release:badge', [ "{$filename}" ] );

        // Does file exists?
        $this->assertFileExists($filename);
        $this->assertContains(
            $string,
            file_get_contents($filename)
        );

        // Cleanup
        @unlink($filename);

    }

}