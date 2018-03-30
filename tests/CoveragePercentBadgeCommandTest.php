<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

class CoveragePercentBadgeCommandTest extends CliCommandTest {

    public function testCommand(){

        $cloverxml = ( __DIR__ . '/data/coverage-clover-sample.xml' );
        $filename  = ( __DIR__ . '/data/coverage-badge.svg' );

        $this->silent(
            'coverage:badge',
            [
                "{$filename}",
                "--cloverxml={$cloverxml}"
            ]
        );

        //Does file exists?
        $this->assertFileExists($filename);

        //Cleanup
        @unlink($filename);

    }

    public function testThrowsException(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        //Run the command
        $this->verbose( 'coverage:badge', [
            '--style=unknown'
        ]);

    }

}