<?php
/**
 * Candybar - Coverage HTML Style Command, allows styling the html
 * coverage presentation
 * @author adrian7
 * @version 1.1
 */

namespace DevLib\Candybar\Commands;

use DevLib\Candybar\Util;
use DevLib\Candybar\Coverage\Presentation\Html;
use DevLib\Candybar\Exceptions\UnreadableFileException;

class CoverageHtmlStyleCommand extends Command{

    protected $description =
        "Applies style to the phpunit's coverage html presentation";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'style' => [
            'default'     => 'default',
            'description' => 'The name of the style to be applied'
        ]

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        'root' => [
            'description'   => 'Set the html presentation root folder'
        ]

    ];

    /**
     * Handle command
     * @throws UnreadableFileException
     */
    public function handle() {

        if( $dir = $this->option('root') );
        else{

            //Parse root from phpunit configuration
            $config = Util::getLoggingConfig(
                Util::PHPUNIT_DEFAULT_CONFIG_FILE,
                'coverage-html'
            );

            if( isset($config['target']) )
                $dir = $config['target'];

        }

        if( ! is_dir($dir) )
            throw new UnreadableFileException("Cannot find path {$dir} ... .");

        $presentation = new Html($dir);

        // Set presentation style
        $presentation->setStyle( $this->argument('style') );

    }

}