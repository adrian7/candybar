<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Exceptions;

use Throwable;

class UnknownCommandException extends \Exception{

    public function __construct( $command = "", $code = 0, Throwable $previous = NULL ) {

        //Build message
        $message = ( "No handler registered for command {$command} ... ." . PHP_EOL );
        $message.= "Please check the `bar` section in your configuration file (config.php).";

        parent::__construct( $message, $code, $previous );

    }

}