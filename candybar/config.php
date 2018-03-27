<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

return [

    'defaults' => [],

    //Register here available commands (candies): candybar {command} {--option}
    'bar' => [

        'example:command' =>
            \DevLib\Candybar\Commands\ExampleCommand::class,

        'coverage:style' =>
            \DevLib\Candybar\Commands\CoverageHtmlStyleCommand::class,

        'coverage:theme' => 'Stylecommand.theme',

        'badge:coverage' => 'BadgeGenertor.class',

        'badge:buildstatus' => 'BadgeBuildPasswing.class',

        'upload:s3'         => 'S3Upload.class'
        
    ]

];