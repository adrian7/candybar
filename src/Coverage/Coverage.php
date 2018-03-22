<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace PHPUnit\Candies\Coverage;

use PHPUnit\Candies\Util;

class Coverage{

    /**
     * @var null|Clover
     */
    protected $clover = NULL;

    protected $html = [];

    /**
     * Stats extracted from coverage file
     * @var array
     */
    protected $stats = [];

    /**
     * Lower threshold for code coverage
     * @var int
     */
    protected $lowerThreshold = 60;

    /**
     * Upper threshold for code coverage
     * @var int
     */
    protected $upperThreshold = 90;

    /**
     * Colors for badges
     * @var array
     */
    protected $colors = [
        'red'       => 'F51455', //Under lower threshold
        'pink'      => 'F8B1C1', //Close to lower threshold
        'yellow'    => 'F9C134', //Just over the lower threshold
        'orange'    => 'F78500', //Close to upper threshold
        'lime'      => 'A7E8BD', //Just over upper threshold
        'green'     => '63B931', //Over upper threshold with 50%
    ];

    public function __construct($path) {

        if( is_file($path) ){

            if( $cloverXml = Util::getLoggingConfig($path, 'coverage-clover') )
                //Init clover metrics
                $this->clover  = new Clover($cloverXml);

            //Init html config
            $htmlCfg = Util::getLoggingConfig($path, 'coverage-html');

            if( isset($htmlCfg['lowUpperBound']) )
                $this->lowerThreshold = intval($htmlCfg['lowUpperBound']);

            if( isset($htmlCfg['highLowerBound']) )
                $this->upperThreshold = intval($htmlCfg['highLowerBound']);

            //TODO init html dir too
            //if( $cloverXml = Util::getLoggingConfig($path, 'coverage-clover') )
            //    $this->coverageHtml = Util::getLoggingConfig($path, 'coverage-html');

        }

        if( is_dir($path) ){

            $this->clover = new Clover(
                [
                    'target' => rtrim($path, DIRECTORY_SEPARATOR) .
                             DIRECTORY_SEPARATOR .
                             'clover.xml'
                ]
            );

        }

    }

    public function getStats(){
        //TODO...
    }

    public function getPercent(){
        //TODO...
    }

    public function getLowerThreshold(){
        return $this->lowerThreshold;
    }

    public function getUpperThreshold(){
        return $this->upperThreshold;
    }

    public function applyStyle($name){
        //TODO apply style to html...
    }

    public function generateBadge($path, $prefix='coverage', $color='auto', $type){
        //TODO...
    }
}