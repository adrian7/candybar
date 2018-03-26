<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

abstract class Command extends CLI {

    protected $options;

    public function setup( Options $options ) {
        // TODO: Implement setup() method.
    }

}