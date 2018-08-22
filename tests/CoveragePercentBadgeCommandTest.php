<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

class CoveragePercentBadgeCommandTest extends CliCommandTest {

    /**
     * @throws Exception
     */
    public function testCommand(){

        $cloverxml = ( __DIR__ . '/data/coverage/clover-sample.xml' );
        $filename  = ( __DIR__ . '/data/coverage-badge.svg' );

        $this->silent(
            'coverage:badge',
            [
                "{$filename}",
                "--cloverxml={$cloverxml}"
            ]
        );

        // Does file exists?
        $this->assertFileExists($filename);

        // Cleanup
        @unlink($filename);

    }


    /**
     * @throws Exception
     */
    public function testBadgeColorChangeOnHigherCoverage(){

        $cloverxml = ( __DIR__ . '/data/coverage/clover-sample-2.xml' );
        $filename  = ( __DIR__ . '/data/coverage-badge-2.svg' );

        $this->silent(
            'coverage:badge',
            [
                "{$filename}",
                "--cloverxml={$cloverxml}"
            ]
        );

        // Does file exists?
        $this->assertFileExists($filename);

        // Is the badge green?
        $this->assertContains('F78500', file_get_contents($filename));

        // Cleanup
        @unlink($filename);

    }

    /**
     * @throws Exception
     */
    public function testBadgeColorGreenOnFullCoverage(){

        $cloverxml = ( __DIR__ . '/data/coverage/clover-sample-green.xml' );
        $filename  = ( __DIR__ . '/data/coverage-badge-green.svg' );

        $this->silent(
            'coverage:badge',
            [
                "{$filename}",
                "--cloverxml={$cloverxml}"
            ]
        );

        // Does file exists?
        $this->assertFileExists($filename);

        // Is the badge green?
        $this->assertContains('63B931', file_get_contents($filename));

        // Cleanup
        @unlink($filename);

    }

    /**
     * @throws Exception
     */
    public function testThrowsException(){

        // Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        // Run the command
        $this->verbose( 'coverage:badge', [
            '--style=unknown'
        ]);

    }

}