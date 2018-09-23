<?php
/**
 * Candybar - Custom command for tests
 * @author adrian7
 * @version 1.0
 */

class CustomCommand extends \DevLib\Candybar\Commands\Command{


    protected $arguments = [

        'first' => [
            'required'      => TRUE,
            'description'   => 'This argument is required'
        ],

        'second' => [
            'value'         => 'default',
            'description'   => 'This argument isn\'t required'
        ]

    ];

    protected $options = [];

}