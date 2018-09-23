<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

require_once __DIR__ . '/helpers/CustomCommand.php';

class CustomCommandTest extends \PHPUnit\Framework\TestCase{

    public function testThrowsExceptionForMissingArguments(){

        //Expects exception to be thrown
        $this->expectException( InvalidArgumentException::class );

        $_SERVER['argv'] = $args = [ 'custom:command' ];

        $cmd = new CustomCommand();

        $cmd->run($args);

    }

    public function testThrowsExceptionForInvalidArgType(){

        //Expects exception to be thrown
        $this->expectException( InvalidArgumentException::class );

        $_SERVER['argv'] = $args = [
            'custom:command',
            'first',
            'second'
        ];

        $cmd = new CustomCommand();

        $cmd->argument( new stdClass() );

    }

    public function testThrowsExceptionForInvalidOptType(){

        //Expects exception to be thrown
        $this->expectException( InvalidArgumentException::class );

        $_SERVER['argv'] = $args = [
            'custom:command',
            'first',
            'second'
        ];

        $cmd = new CustomCommand();

        $cmd->option( new stdClass() );

    }

}