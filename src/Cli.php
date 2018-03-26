<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Commands\Command;
use DevLib\Candybar\Commands\CommandInterface;
use DevLib\Candybar\Exceptions\UnknownCommandException;
use DevLib\Candybar\Exceptions\UnreadableFileException;
use DevLib\Candybar\Exceptions\InvalidConfigurationException;

class Cli extends Command {

    const VERSION = '0.1';

    const CODENAME= 'Butterfinger';

    /**
     * Configured commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Flag - version printed
     * @var bool
     */
    protected $versionPrinted = FALSE;

    /**
     * Loads configuration
     *
     * @throws InvalidConfigurationException
     * @throws UnreadableFileException
     */
    public function config(){

        $config   = NULL;
        $filename = Util::findFileOrFail('config.php',  [
            'candybar',
            ( __DIR__ . '/../candybar' )
        ]);

        if( $filename )
            //Load configuration
            $config = require $filename;

        if( ! is_array($config) or ! isset($config['bar']) )
            throw new InvalidConfigurationException($filename);

        //Configure commands
        $this->commands = data_get($config, 'bar');

    }

    /**
     * Prints CLI usage
     * @param null|string $command
     */
    protected function showUsage($command=NULL) {

        $argv     = $_SERVER['argv'];
        $filename = basename( array_shift($argv) );

        $this->showVersion(TRUE);

        if( $command )
            //Execute requested command's help
            return $this->execute($command, ['--help']);

        //Print CLI usage instructions

        print <<<EOT
        
Usage: {$filename} [command] {options}

Commands: 

 help       prints this message  
 version    prints version information
 list       lists all available commands


EOT;

    }

    /**
     * Prints available commands
     */
    protected function showList(){
        //TODO...
    }

    /**
     * Prints version information
     * @param bool $short
     */
    protected function showVersion($short=FALSE){

        if( $this->versionPrinted  )
            return;

        $version  = self::VERSION;
        $codename = self::CODENAME;

        if( $short )

            print <<<EOT
            
 🍬 Candybar v{$version} ({$codename})
 
EOT;

        else

            print <<<EOT

 🍬 Candybar v{$version} ({$codename})
 
 Project page:  https://github.com/adrian7/candybar
 Documentation: https://github.com/adrian7/candybar/wiki
 

EOT;


        $this->versionPrinted = TRUE;

    }

    /**
     * Main - entry point
     *
     * @param bool $exit
     *
     * @return mixed
     * @throws InvalidConfigurationException
     * @throws UnknownCommandException
     * @throws UnreadableFileException
     * @throws \ReflectionException
     */
    public static function main($exit = TRUE) {

        $command = new static(FALSE);

        //configure
        $command->config();

        return $command->run($_SERVER['argv'], $exit);

    }

    /**
     * Run
     *
     * @param array $argv
     * @param bool $exit
     *
     * @return int|void
     * @throws UnknownCommandException
     * @throws \ReflectionException
     */
    public function run(array $argv, $exit=FALSE){

        if( count($argv) and isset($argv[1]) and $command = trim($argv[1]) ){

            //Init command
            array_shift($argv);

            switch ($command){

                case 'list':
                    return $this->showList();

                case 'version':
                    return $this->showVersion();

                case 'help':
                    return $this->showUsage();

                default:
                    //Execute command
                    $this->execute($command, $argv);
            }

        }
        else
            $this->showUsage();

    }

    /**
     * Executes a given command
     *
     * @param string $cmd
     * @param array $argv
     */
    public function execute($cmd, $argv){

        if( in_array($cmd, array_keys($this->commands) ) ){

            $handler = $this->commands[$cmd];

            if( class_exists( $this->commands[$cmd] ) )
                $handler = new $handler;
            else
                $this->exitWithError(
                    new \ReflectionException(
                        "Cannot find class {$handler} for command {$cmd} ... ."
                    )
                );

            //Run command with arguments
            if( is_a($handler, CommandInterface::class) )

                //Do-ya-thing
                ( '--help' == $argv[0] ) ?
                    $handler->showHelp( $argv ) :
                    $handler->run( $argv );

            else {

                //No face no name no number

                $classname = get_class($handler);
                $required  = CommandInterface::class;

                $this->exitWithError(
                    new \ReflectionException(
                        "Invalid handler provided `{$classname}`.
                        Command handlers should implement `$required` ."
                    )
                );

            }

        }
        else
            //Unrecognized command
            $this->exitWithError( new UnknownCommandException($cmd) );

    }

}