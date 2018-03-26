<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use Laravie\Parser\Xml\Reader;
use Laravie\Parser\Xml\Document;

class Util{

    /**
     * Logging config
     * @var null|array
     */
    protected static $loggingCfg = NULL;

    /**
     * Clover metrics
     * @var null|array
     */
    protected static $cloverMetrics = NULL;

    /**
     * Looks up file in paths. This function is not recursive
     *
     * @param string $name
     * @param string $ext
     * @param array $paths
     * @param bool $cwd
     *
     * @return bool|string
     */
    public static function lookupFile($name, $ext='', $paths=[DIRECTORY_SEPARATOR], $cwd=TRUE){

        $name = ltrim($name, DIRECTORY_SEPARATOR);
        $paths= is_array($paths) ? $paths : [$paths];

        if( ! is_string($paths[0]) )
            throw new \InvalidArgumentException("Paths parameter should be a list of strings... .");

        if( $ext ){

            $ext = ".{$ext}";

            //Check if the file has .ext

            if( $ext == mb_substr( mb_strtolower($name), -( mb_strlen($ext) ) ) );
            else
                //Add extension
                $name.= $ext;

        }

        //Lookup in paths
        foreach ($paths as $path )
            if(
                is_dir($path)
                and
                ( $file = ( $path . DIRECTORY_SEPARATOR . $name ) )
                and
                is_file($file)
            )
                return realpath($file);

        //Lookup in CWD
        if( $cwd and is_file($name) )
            return realpath($name);

        return FALSE;

    }

    /**
     * Parses xml element(s) by query
     *
     * @param string $path: xml file path
     * @param array $query: query to lookup elements
     *
     * @return array
     */
    public static function parseXml($path, $query){

        if( ! file_exists( $path ) )
            throw new \InvalidArgumentException("File at {$path} cannot read... .");

        $xml = ( new Reader( new Document() ) )->load( $path );

        return $xml->parse($query);
    }

    /**
     * Rounds a number down
     *
     * @param integer|float $number
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
            $cfg = self::parseXml($path, [
                'logging' => [ 'uses' => 'logging.log[::type>type,::target>target]' ]
            ]);

            self::$loggingCfg = ( $cfg and isset($cfg['logging']) ) ?
                $cfg['logging'] :
                [];

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
            $metrics = self::parseXml($path, [
                'metrics' => [ 'uses' => "project.metrics[::{$metrics}]" ]
            ]);

            self::$cloverMetrics = ( $metrics and isset($metrics['metrics'][0]) ) ?
                $metrics['metrics'][0] :
                [];

        }

        if( empty($metric) )
            //Return all available types
            return self::$cloverMetrics;

        //Filter array for metric
        return array_first(
            self::$cloverMetrics,
            function ($value, $key) use($metric){

                return $metric == $key;

            },
            NULL
        );

    }

}