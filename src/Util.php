<?php
/**
 * Candybar - Utility class
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Exceptions\UnreadableFileException;
use Laravie\Parser\Xml\Reader;
use Laravie\Parser\Xml\Document;
use Symfony\Component\Filesystem\Filesystem;

class Util{

    /**
     * PHPUnit default configuration file
     */
    const PHPUNIT_DEFAULT_CONFIG_FILE = 'phpunit.xml';

    /**
     * String to identify standard output
     */
    const STANDARD_OUTPUT = 'standard';

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
     * @param bool $fail
     *
     * @return bool|string
     *
     * @throws UnreadableFileException
     */
    public static function lookupFile(
        $name,
        $ext='',
        $paths=[],
        $cwd=TRUE,
        $fail=FALSE
    ){

        $name = ltrim($name, DIRECTORY_SEPARATOR);
        $paths= is_array($paths) ? $paths : [$paths];

        if( empty($paths) )
            //Defaults to current directory
            $paths = [ getcwd() ];

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

        if( $fail )
            //File not found
            throw new UnreadableFileException(
                $name,
                ( $cwd ? array_merge($paths, [getcwd()] ) : $paths )
            );

        //Return false
        return FALSE;

    }

    /**
     * @param $name
     * @param array $paths
     * @param bool $ext
     * @param bool $cwd
     *
     * @return bool|string
     * @throws UnreadableFileException
     */
    public static function findFileOrFail($name, $paths=[], $ext=FALSE, $cwd=TRUE){

        if( $path = self::lookupFile($name, $ext, $paths, $cwd, TRUE) )
            return $path;

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

    /**
     * Rounds file size and to lowest applicable multiple
     *
     * @param string $path
     * @param int $decimals
     *
     * @return string
     */
    public static function getSizeHuman($path, $decimals=1){

        $bytes  = self::getSize($path);
        $sz     = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return
            sprintf(
                "%.{$decimals}f",
                $bytes / pow(1024, $factor)
            ) .
            @$sz[$factor];

    }

    /**
     * Calculates size of the path
     *
     * @param string $path
     *
     * @return int
     */
    public static function getSize($path){

        if( is_file($path) )
            //Is a file
            return filesize($path);

        //Is a folder
        $size    = 0;
        $pattern = ( rtrim($path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . '*' );

        foreach ( glob($pattern, GLOB_NOSORT ) as $each)
            //Recurse to get size of each item
            $size += is_file($each) ? filesize($each) : self::getSize($each);

        return $size;

    }

    /**
     * Copies source directory to destination
     *
     * @param string $source
     * @param string $destination
     * @param int $mode
     *
     * @return string
     */
    public static function copyDir($source, $destination='/', $mode=0755){

        $sys = new Filesystem();

        $destination = rtrim($destination, DIRECTORY_SEPARATOR);
        $source      = rtrim($source, DIRECTORY_SEPARATOR);

        if( ! is_dir($destination) )
            //Create destination if it does not exist
            $sys->mkdir($destination, $mode);

        if( ! is_dir($destination) )
            throw new \InvalidArgumentException("Could not create folder {$destination} ... .");

        //Initialize an iterator
        $directoryIterator = new \RecursiveDirectoryIterator(
            $source,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );

        $iterator = new \RecursiveIteratorIterator(
            $directoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {

            $path = ( $destination .  DIRECTORY_SEPARATOR . $iterator->getSubPathName() );

            if ( $item->isDir() )
                $sys->mkdir($path);
            else
                //Overwrite item
                $sys->copy($item, $path);

        }

        //Return copied path
        return $destination;

    }

    /**
     * Prints a string
     * @param string $string
     * @param string $channel
     */
    public static function out($string, $channel=self::STANDARD_OUTPUT){

        //TODO add support for sprintf and handle gracefully colorized output

        if( self::STANDARD_OUTPUT == $channel )
            //Use standard output
            print $string;

        else
            //Print to file
            file_put_contents($channel, $string, FILE_APPEND);

    }

}