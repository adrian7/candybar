<?php
/**
 * Candybar - CLI tests
 * @author adrian7
 * @version 1.0
 */

class CliTest extends CliCommandTest {

    public function testInitCommand(){

        //Backup cwd
        $backupCWD = getcwd();
        $newCWD    = ( __DIR__ . '/data' );
        $installDir= ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' )    ;

        //Change cwd
        chdir(__DIR__ . '/data');

        $this->silent('init', [], [
            'Welcome',
            \DevLib\Candybar\Cli::VERSION
        ]);

        //Did the files were copied?
        $this->assertFileExists(
            $installDir . DIRECTORY_SEPARATOR . 'config.php'
        );

        $this->assertFileExists(
            $installDir . DIRECTORY_SEPARATOR . 'styles/default.css'
        );

        //Restore cwd
        chdir($backupCWD);

        //Cleanup
        $sys = new \Symfony\Component\Filesystem\Filesystem();
        $sys->remove($installDir);

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\InvalidConfigurationException
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testHelpCommand(){

        //Reset command runner
        $this->setUp(TRUE);

        $this->silent('help', [], [
            'help',
            'Usage',
            \DevLib\Candybar\Cli::VERSION
        ]);

    }

    public function testListCommand(){

        //Test list command with default commands
        $this->silent('list', [], [
            'coverage:style',
            'coverage:badge',
            'example:command'
        ]);

    }

    public function testUnknownCommand(){

        //Expecting an exception
        $this->expectException(
            \DevLib\Candybar\Exceptions\UnknownCommandException::class
        );

        $this->verbose('unknown');

    }

}