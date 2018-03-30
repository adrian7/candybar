<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

class ExampleCommandTest extends CliCommandTest {

    public function testInputArguments(){

        $account = str_random(5);
        $argOne  = str_random(3);
        $argTwo  = str_random(4);

        $this->execute(
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

    public function testThrowsException(){

        //Expects exception to be thrown
        $this->expectException(InvalidArgumentException::class);

        //Run the command
        $this->verbose( 'example:command');

    }
}