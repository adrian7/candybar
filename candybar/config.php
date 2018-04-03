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

        'coverage:badge' =>
            \DevLib\Candybar\Commands\CoveragePercentBadgeCommand::class,

        'coverage:style' =>
           \DevLib\Candybar\Commands\CoverageHtmlStyleCommand::class,

        'license:badge' =>
            \DevLib\Candybar\Commands\LicenseBadgeCommand::class,

        //'badge:buildstatus' => 'BadgeBuildPasswing.class',

        //'upload:s3'         => 'S3Upload.class'

        'readme:add-badges' =>
            \DevLib\Candybar\Commands\AddBadgesToReadmeCommand::class

    ]

];