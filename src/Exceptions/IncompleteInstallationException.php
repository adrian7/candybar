<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Exceptions;

use Throwable;

class IncompleteInstallationException extends \Exception{


    public function __construct( $missing = "", $code = 0, Throwable $previous = NULL ) {

        //Make sure it's a string
        $missing = strval($missing);

        $message = ("The file or folder {$missing} can't be located ... ." . PHP_EOL );
        $message.= (
            "This issue indicates an incomplete installation, 
            please remove the candybar folder and run init command again."
        );

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

}