<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CliTest extends CliCommandTest {

    public function testHelpCommand(){

        $this->runCommandTest('help', [], [
            'help',
            'Usage',
            \DevLib\Candybar\Cli::VERSION
        ]);

    }

    public function testListCommand(){

        //Test list command with default commands
        $this->runCommandTest('list', [], [
            'coverage:style',
            'badge:coverage',
            'example:command'
        ]);

    }

}