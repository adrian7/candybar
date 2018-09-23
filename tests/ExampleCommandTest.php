<?php
/**
 * Candybar - Example Command Class Test
 * @author adrian7
 * @version 1.3
 */

class ExampleCommandTest extends CliCommandTest {

    public function testInputArguments(){

        $account = str_random(5);
        $argOne  = str_random(3);
        $argTwo  = str_random(4);

        $this->silent(
            'example:command',
            [
                "{$argOne}",
                "{$argTwo}",
                "--account={$account}",
            ],
            [$account, $argOne, $argTwo]
        );

    }

    public function testInputOptions(){

        $account = str_random(5);
        $key     = str_random(8);

        $this->silent(
            'example:command',
            [
                "--account={$account}",
                "--key={$key}"
            ],
            [$account, $key]
        );

    }

    public function testIfShowsHelp(){

        $this->silent( 'example:command', ['--help'], [
            'Usage:',
            'account',
            'erase'
        ]);

    }

    public function testThrowsExceptionForRequiredOption(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        //Run the command
        $this->verbose( 'example:command');

    }

    public function testThrowsExceptionForUnspecifiedInput(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        $command = new \DevLib\Candybar\Commands\ExampleCommand( self::$output );
        $command->argument('unspecified');

    }
}