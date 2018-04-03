<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CliTest extends CliCommandTest {

    public function testInitCommand(){

        //Backup cwd
        $backupCWD = getcwd();

        //Change cwd
        chdir(__DIR__ . '/data');

        $this->silent('init', [], [
            'Welcome',
            \DevLib\Candybar\Cli::VERSION
        ]);

        //Did the files were copied?
        $this->assertFileExists(
            getcwd() . DIRECTORY_SEPARATOR . 'config.php'
        );

        $this->assertFileExists(
            getcwd() . DIRECTORY_SEPARATOR . 'styles/default.css'
        );

        //Restore cwd
        chdir($backupCWD);

        //Cleanup
        //TODO remove directory

    }

    public function testHelpCommand(){

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