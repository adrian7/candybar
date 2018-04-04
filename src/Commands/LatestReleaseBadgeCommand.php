<?php
/**
 * Candybar - Command to generate latest release badge
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use DevLib\Candybar\Cli;
use DevLib\Candybar\Graphics\BadgeGenerator;

class LatestReleaseBadgeCommand extends Command{

    protected $description =
        "Generates a badge for the latest release";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'filename' => [
            'default'     => 'release-badge.svg',
            'description' => 'Filename to export.'
        ]

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        'style' => [
            'default'     => 'svg',
            'description' =>
                'The style for the badge. Supported styles are: plastic, flat and flat-square'
        ],

    ];

    protected function getLatestRelease(){
        return ( 'v' . Cli::VERSION . ' (' . Cli::CODENAME . ')' );
    }

    protected function getReleaseColor(){
        return Cli::RELEASE_COLOR;
    }

    /**
     * Handle command
     */
    public function handle() {

        $filename= $this->argument('filename');
        $style   = $this->option('style');

        //Generate  badge
        BadgeGenerator::make(
            "latest",
            $this->getLatestRelease(),
            $this->getReleaseColor(),
            $style
        )->save($filename);

    }

}