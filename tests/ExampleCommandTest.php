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

    public function testThrowsExceptionForRequiredOptionWhenChained(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        $_SERVER['argv'] = $args = [
            'bin/executable',
            'example:command',
            '--account= --secret'
        ];

        // Handle arguments
        self::$runner->run($args);

    }

    public function testThrowsExceptionForOptionValueMissing(){

        //Expects exception to be thrown
        $this->expectException(\PHPUnit\Framework\Exception::class);

        $_SERVER['argv'] = $args = [
            'bin/executable',
            'example:command',
            '--account='
        ];

        // Handle arguments
        self::$runner->run($args);

    }

    public function testThrowsExceptionForUnrecognizedOption(){

        //Expects exception to be thrown
        $this->expectException( \PHPUnit\Framework\Exception::class );

        $this->verbose('example:command', [
            '--account=test',
            '--unrecognized'
        ]);

    }

    public function testThrowsExceptionForTooManyArguments(){

        //Expects exception to be thrown
        $this->expectException( InvalidArgumentException::class );

        $this->verbose('example:command', [
            'argone',
            'argtwo',
            'argthree',
            '--account=test'
        ]);

    }

    public function testThrowsExceptionForUnspecifiedInput(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        $command = new \DevLib\Candybar\Commands\ExampleCommand( self::$output );
        $command->argument('unspecified');

    }

}