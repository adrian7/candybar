<?php
/**
 * Candybar - Command to generate the license badge
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use Carbon\Carbon;
use DevLib\Candybar\Graphics\BadgeGenerator;

class BuildDateBadgeCommand extends Command{

    protected $description =
        "Generates build date badge.";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'filename' => [
            'default'     => 'builddate-badge.svg',
            'description' => 'Filename to export.'
        ]

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        'color' => [
            'default'       => '4f5b93',
            'description'   => 'The badge color. Defaults to #4f5b93'
        ],

        'style' => [
            'default'     => 'svg',
            'description' =>
                'The style for the badge. Supported styles are: plastic, flat and flat-square'
        ],

        'format' => [
            'default'       => 'Y-m-d',
            'description'   => 'The date format. Defaults to Y-m-d'
        ],

        'timezone' => [
            'default'       => 'UTC',
            'description'   => 'The timezone to use. Defaults to UTC'
        ]

    ];

    /**
     * Handle command
     */
    public function handle() {

        $filename= $this->argument('filename');

        $color   = $this->option('color');
        $style   = $this->option('style');
        $format  = $this->option('format');
        $tz      = $this->option('timezone');

        $date = Carbon::now($tz);

        //Generate  badge
        BadgeGenerator::make(
            "built",
            $date->format($format),
            $color,
            $style
        )->save($filename);

    }

}