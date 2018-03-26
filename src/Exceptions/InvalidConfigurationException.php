<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Exceptions;

use Throwable;

class InvalidConfigurationException extends \Exception{


    public function __construct( $filename = "", $code = 0, Throwable $previous = NULL ) {

        //Make sure it's a string
        $filename = strval($filename);

        parent::__construct(
            "Invalid configuration found in {$filename}... .",
            $code,
            $previous
        );
    }

}