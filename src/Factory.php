<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Graphics\BadgeGeneratorInterface;
use DevLib\Candybar\Transport\FileTransportInterface;
use DevLib\Candybar\Coverage\Stats\StatisticsInterface;
use DevLib\Candybar\Coverage\Presentation\PresentationInterface;

class Factory{

    /**
     * Supported makers
     * @var array
     */
    protected static $supported = [
        Makers::COVERAGE_STATISTICS     => StatisticsInterface::class,
        Makers::COVERAGE_PRESENTATION   => PresentationInterface::class,
        Makers::GRAPHICS_BADGES         => BadgeGeneratorInterface::class,
        Makers::FILE_TRANSPORT          => FileTransportInterface::class
    ];

    /**
     * @param $for
     * @param \Closure $callable
     *
     * @throws Exceptions\IoCRepositoryException
     */
    public static function registerMaker($for, \Closure $callable){

        if( ! in_array($for, array_keys(self::$supported) ) )
            throw new \InvalidArgumentException("Unsupported maker type {$for}... .");

        Repository::register($for, $callable);

    }

    /**
     * @return mixed
     * @throws Exceptions\IoCRepositoryException
     */
    public static function createCoveragePresentation(){
        return Repository::resolve(
            Makers::COVERAGE_PRESENTATION
        );
    }

    public static function createCoverageStatistics(){
        //TODO...
    }

    public function createBadgeGenerator(){
        //TODO...
    }

    public static function createFileTransport(){
        //TODO...
    }

}