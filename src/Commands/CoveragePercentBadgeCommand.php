<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use DevLib\Candybar\Coverage\Stats\Clover;
use DevLib\Candybar\Graphics\BadgeGenerator;
use DevLib\Candybar\Util;

class CoveragePercentBadgeCommand extends Command{

    protected $description =
        "Generates coverage percent badge";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'filename' => [
            'default'     => 'coverage-percent.svg',
            'description' => 'Filename to export'
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

        'cloverxml' => [
            'description' => 'The path to the clover coverage xml file'
        ]

    ];

    /**
     * Lower threshold for code coverage
     * @var int
     */
    protected $lowThreshold = 60;

    /**
     * Upper threshold for code coverage
     * @var int
     */
    protected $highThreshold = 90;

    /**
     * Colors for badge
     * @var array
     */
    protected $colors = [
        'red'       => 'F51455', // Under lower threshold
        'pink'      => 'F65676', // Close to lower threshold
        'yellow'    => 'F9C134', // Just over the lower threshold
        'orange'    => 'F78500', // Close to upper threshold
        'lime'      => '11CD86', // Just over upper threshold
        'green'     => '63B931', // Over upper threshold with 50%
    ];

    /**
     * Selects badge color based on coverage percent
     * @param $coverage
     *
     * @return mixed
     */
    protected function badgeColor($coverage){

        $colors = array_values($this->colors);

        // Calculate color points
        $points = [
            $this->lowThreshold/2 + $this->lowThreshold/4,
            $this->lowThreshold,
            $this->lowThreshold + $this->lowThreshold/5,
            $this->highThreshold/2 + $this->highThreshold/2.8,
            $this->highThreshold/2 + $this->highThreshold/2.3,
            $this->highThreshold
        ];

        // Red
        if( $coverage <= $points[0] )
            return array_shift($colors);

        foreach ($points as $i=>$point)
            if( $coverage > $point )
                continue;
            else
                return $colors[$i];

        // Green
        return array_pop($colors);

    }

    /**
     * Handle command
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function handle() {

        $style    = $this->option('style');
        $filename = $this->argument('filename');

        if( $cloverxml = $this->option('cloverxml') );
        else{

            //Retrieve clover config from phpunit
            $config   = Util::getLoggingConfig(
                Util::findPhpUnitConfigFile(), 'coverage-clover'
            );

            $cloverxml = isset($config['target']) ? $config['target'] : NULL;

        }

        //Init coverage object
        $coverage = new Clover( $cloverxml );
        $percent  = $coverage->coveragePercent('all', TRUE);

        //Generate coverage percent badge
        BadgeGenerator::make(
            "coverage",
            ( $percent . '%' ),
            $this->badgeColor($percent),
            $style
        )->save($filename);

    }

}