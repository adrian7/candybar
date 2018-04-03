<?php
/**
 * Candybar - Command to generate the license badge
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use DevLib\Candybar\Graphics\BadgeGenerator;

class LicenseBadgeCommand extends Command{

    protected $description =
        "Generates the a license badge";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'license' => [
            'default'     => 'MIT',
            'description' => 'The license to short name. E.g. MIT or GPL3'
        ],

        'filename' => [
            'default'     => 'license-badge.svg',
            'description' => 'Filename to export.'
        ]

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        'color' => [
            'default'       => '428F7E',
            'description'   => 'The badge color. Defaults to #428F7E'
        ],

        'style' => [
            'default'     => 'svg',
            'description' =>
                'The style for the badge. Supported styles are: plastic, flat and flat-square'
        ],

    ];

    /**
     * Handle command
     */
    public function handle() {

        $license = $this->argument('license');
        $filename= $this->argument('filename');

        $color   = $this->option('color');
        $style   = $this->option('style');

        //Generate  badge
        BadgeGenerator::make(
            "license",
            $license,
            $color,
            $style
        )->save($filename);

    }

}