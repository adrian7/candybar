<?php
/**
 * Candybar - Unreadable File Exception
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Exceptions;

class UnreadableFileException extends \Exception{

    public function __construct( $filename = "", $paths=[] ) {

        $path = ( 1 < count($paths) ) ? join(" or ", $paths) : join("", $paths);

        $message = sprintf(
            "Could not locate or access file %s . 
            Please make sure the file exists and is readable in %s.",
            $filename,
            $path
        );

        parent::__construct($message);

    }

}