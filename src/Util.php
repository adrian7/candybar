<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace PHPUnit\Candies;

use Laravie\Parser\Xml\Reader;
use Laravie\Parser\Xml\Document;

class Util{

    protected static $loggingCfg = NULL;

    protected static $cloverMetrics = NULL;

    public static function parseXml($path, $query){

        if( ! file_exists( $path ) )
            throw new \InvalidArgumentException("File at {$path} cannot read... .");

        $xml = ( new Reader( new Document() ) )->load( $path );

        return $xml->parse($query);
    }

    /**
     * Rounds a number down
     *
     * @param $number
     * @param int $decimals
     * @param int $mode
     *
     * @return float|int
     */
    public static function round($number, $decimals=2, $mode=PHP_ROUND_HALF_DOWN){

        return ( 0 >= $decimals ) ?
            intval( round($number, 0, $mode) ) :
            round($number, $decimals, $mode);

    }

    /**
     * @param $path
     * @param null $type
     *
     * @return array|mixed|null
     */
    public static function getLoggingConfig($path, $type=NULL){

        if( empty(self::$loggingCfg) ){

            //Initialize config
            self::$loggingCfg = self::parseXml($path, [
                'logging' => [ 'uses' => 'logging.log[::type>type,::target>target]' ]
            ]);

        }

        if( empty($type) )
            //Return all available types
            return self::$loggingCfg;

        //Filter array for type
        return array_first(self::$loggingCfg, function ($value, $key) use($type){

            return
                is_array($value) and
                isset($value['type']) and
                ( $type ==  $value['type']);

        }, NULL);

    }

    /**
     * @param $path
     * @param null|string $metric
     *
     * @return array|null
     */
    public static function getCloverXmlMetrics($path, $metric=NULL){

        if( empty(self::$cloverMetrics) ){

            //List of metrics to extract
            $metrics = @join(',::', [
                'files>files',
                'loc>loc',
                'ncloc>ncloc',
                'classes>classes',
                'methods>methods',
                'coveredmethods>coveredmethods',
                'conditionals>conditionals',
                'coveredconditionals>coveredconditionals',
                'statements>statements',
                'coveredstatements>coveredstatements',
                'elements>elements',
                'coveredelements>coveredelements',
            ]);

            //Initialize config
            self::$cloverMetrics = self::parseXml($path, [
                'metrics' => [ 'uses' => "project.metrics[::{$metrics}]" ]
            ]);

        }

        if( empty($metric) )
            //Return all available types
            return self::$cloverMetrics;

        //Filter array for metric
        $element = array_first(
            self::$loggingCfg,
            function ($value, $key) use($metric){

                return
                    is_array($value) and
                    isset($value[$metric]);

            },
            NULL
        );

        return ( $element and isset($element[$metric]) ) ? $element[$metric] : NULL;

    }
}