<?php
/**
 * This is an invalid Candybar configuration file
 */
return [

    'bar' => [

        'invalid:command' => 'Some\Missing\ClassPath',
        'missing:class'   => 'Another\Missing\ClassPath',
        'implements:wrong'=> stdClass::class
    ]

];