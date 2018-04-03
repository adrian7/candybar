<?php
/**
 * Candybar - Configuration file
 * @author adrian7
 * @version 1.1
 */

return [

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

        'build:badge:date' =>
            \DevLib\Candybar\Commands\BuildDateBadgeCommand::class,

        //'s3:upload'         => 'S3Upload.class'

        'readme:add-badges' =>
            \DevLib\Candybar\Commands\AddBadgesToReadmeCommand::class

    ]

];