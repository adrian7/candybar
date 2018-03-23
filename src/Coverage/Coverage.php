<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Coverage;

use DevLib\Candybar\Util;
use DevLib\Candybar\Coverage\Stats\Clover;
use DevLib\Candybar\Coverage\Presentation\Html;
use DevLib\Candybar\Coverage\Stats\StatisticsInterface;
use DevLib\Candybar\Coverage\Presentation\PresentationInterface;

class Coverage{

    const TYPE_CLOVER = 'coverage-clover';

    const TYPE_HTML   = 'coverage-html';

    /**
     * @var null|StatisticsInterface
     */
    protected $stats = NULL;

    /**
     * @var null|PresentationInterface
     */
    protected $presentation = NULL;

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

            //Init from phpunit config file

            if(
                $cloverConfig = Util::getLoggingConfig($path, 'coverage-clover')
                    and
                $filename = $cloverConfig['target']
            )
                //Init stats
                $this->buildStats( $filename, $cloverConfig['type'] );
            else
                //TODO add support for coverage drivers with factory
                throw new \InvalidArgumentException("Missing logging type `coverage-clover` in your phpunit config... .");

            //Init html presentation
            if( $htmlConfig = Util::getLoggingConfig($path, 'coverage-html') ){

                if( isset($htmlConfig['lowUpperBound']) )
                    $this->lowThreshold = intval($htmlConfig['lowUpperBound']);

                if( isset($htmlConfig['highLowerBound']) )
                    $this->highThreshold = intval($htmlConfig['highLowerBound']);

                $this->buildPresentation($htmlConfig['target'], $htmlConfig['type']);
            }
            else
                //TODO add support for coverage drivers with factory
                throw new \InvalidArgumentException("Missing logging type `coverage-html` in your phpunit config... .");

        }

        if( is_dir($path) ){

            $path = rtrim($path, DIRECTORY_SEPARATOR);

            if( $file = ( $path . DIRECTORY_SEPARATOR . 'coverage.xml' )  and is_file($file) )
                $this->buildStats($file, self::TYPE_CLOVER);
            else
                throw new \InvalidArgumentException(
                    "Missing file coverage.xml in your phpunit output folder at $path ... ."
                );

            if( $file = ( $path . DIRECTORY_SEPARATOR . 'index.html' )  and is_file($file) )
                $this->buildPresentation($file, self::TYPE_HTML);
            else
                throw new \InvalidArgumentException(
                    "Missing file index.html in your phpunit output folder at $path ... ."
                );

        }

    }

    /**
     * Initialize clover stats
     *
     * @param string $filename
     * @param string $type
     */
    public function buildStats($filename, $type=self::TYPE_CLOVER){

        switch ($type){

            case self::TYPE_CLOVER:
                $this->stats = new Clover($filename); break;

            default:
                throw new \InvalidArgumentException("Unsupported coverage type {$type} ... .");

        }
    }

    /**
     * Initialize presentation html
     *
     * @param string $filename
     * @param string $type
     */
    public function buildPresentation($filename, $type=self::TYPE_HTML){

        switch ($type){

            case self::TYPE_HTML:
                $this->presentation = new Html($filename); break;

            default:
                throw new \InvalidArgumentException("Unsupported coverage type {$type} ... .");

        }

    }

    public function getLowerThreshold(){
        return $this->lowThreshold;
    }

    public function getUpperThreshold(){
        return $this->highThreshold;
    }

    public function applyStyle($name){
        //TODO apply style to html...
    }

    public function generateBadge($path, $prefix='coverage', $color='auto', $type){
        //TODO...
    }
}