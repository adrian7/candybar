<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CliTest extends CliCommandTest {

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