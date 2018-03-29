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

        'badge:coverage' =>
            \DevLib\Candybar\Commands\CoveragePercentBadgeCommand::class,

        'coverage:style' =>
           \DevLib\Candybar\Commands\CoverageHtmlStyleCommand::class,

        //'coverage:theme' => 'Stylecommand.theme',

        //'badge:buildstatus' => 'BadgeBuildPasswing.class',

        //'upload:s3'         => 'S3Upload.class'
        
    ]

];