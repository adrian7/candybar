<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Exceptions;

use Throwable;

class UnsupportedFeatureException extends \Exception{

    public function __construct( $feature = "" ) {

        parent::__construct(
            "{$feature} feature is not supported in this version ... ."
        );

    }

}