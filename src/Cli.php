<?php
/**
 * Candybar - CLI, Main entry point
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Commands\Command;
use DevLib\Candybar\Commands\CommandInterface;
use DevLib\Candybar\Exceptions\UnknownCommandException;
use DevLib\Candybar\Exceptions\UnreadableFileException;
use DevLib\Candybar\Exceptions\InvalidConfigurationException;
use DevLib\Candybar\Exceptions\IncompleteInstallationException;
use Symfony\Component\Filesystem\Exception\IOException;

class Cli extends Command {

    /**
     * Version
     */
    const VERSION = '0.3-dev';

    /**
     * Codename
     */
    const CODENAME= 'Grand Bar';

    /**
     * Associated release color
     */
    const RELEASE_COLOR = 'E92F32';

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
     * @param string $filename
     *
     * @throws InvalidConfigurationException
     * @throws UnreadableFileException
     */
    public function config($filename=NULL){

        $config   = NULL;
        $filename = $filename ?: Util::findFileOrFail('config.php',  [
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
     * Installs candybar in $CWD
     *
     * @throws IncompleteInstallationException
     * @throws \InvalidArgumentException
     */
    public function install() {

        $installDir = 'candybar';

        if( ! is_dir( $installDir ) ){

            try{

                // Make install
                Util::copyDir( __DIR__ . '/../candybar', $installDir );

            }
            catch (IOException $e){

                throw new \InvalidArgumentException(
                    sprintf(
                        "Could not install Candybar in %s . 
                        Make sure the path is writable and try again.",
                        getcwd()
                    )
                );

            }

        }

        //Test installation

        if( ! is_dir($installDir . DIRECTORY_SEPARATOR . 'styles')  )
            throw new IncompleteInstallationException("styles");

        if( ! is_file($installDir . DIRECTORY_SEPARATOR . 'config.php')  )
            throw new IncompleteInstallationException('config.php');

        // Roger!
        $this->showVersion(TRUE);
        $this->line(
            sprintf(
                " Welcome! Your config file is at %s . ",
                realpath($installDir . DIRECTORY_SEPARATOR . 'config.php')
            )
        );

    }

    /**
     * Prints CLI usage
     *
     * @param string|null $command
     *
     * @throws \Exception
     */
    protected function showUsage($command=NULL) {

        $argv     = $_SERVER['argv'];
        $filename = basename( array_shift($argv) );

        $this->showVersion(TRUE);

        if( $command )
            //Execute requested command's help
            return $this->execute($command, [ $command, '--help' ]);

        //Print CLI usage instructions

        $this->line(" Usage: {$filename} [command] {options}

 Commands: 

  help       print this message  
  version    print version information
  list       list all available commands (see candybar/config.php)");

        $this->eol();

    }

    /**
     * Prints available commands
     */
    protected function showList(){

        $this->showVersion(TRUE);

        $this->line(" Installed commands: ");
        $this->eol();

        foreach ($this->commands as $command=>$handler)
            $this->eol("    {$command}" );

        $this->line(" Use `candybar help [command]` to get help about a specific command.");
        $this->eol();

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

        // Short version info
        $this->line(" 🍬 Candybar v{$version} ({$codename})");

        if( ! $short ){

            $colors = "\033[46m%1\$s\033[0m\033[43m%1\$s\033[0m\033[45m%1\$s\033[0m";
            $colors.= "\033[42m%1\$s\033[0m\033[41m%1\$s\033[0m\033[44m%1\$s\033[0m";

            $this->line(
                sprintf( $colors, "••••••" )
            );

            // Long version info
            $this->line(" 🏠 Project page:  https://github.com/adrian7/candybar");
            $this->line(" 📚 Documentation: https://github.com/adrian7/candybar/wiki");

            $this->eol();

        }

        $this->versionPrinted = TRUE;

    }

    /**
     * Main - entry point
     *
     * @param bool $exit
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function main( $exit = TRUE ) {

        $command = new static();

        try{

            // Run command
            $command->run($_SERVER['argv'], $exit);

        }
        catch (\Exception $e) {

            // Exit with error
            $command->exitWithError($e);

        }

    }

    /**
     * Run
     *
     * @param array $argv
     * @param bool $exit
     *
     * @return int
     * @throws \Exception
     */
    public function run(array $argv, $exit=FALSE){

        if( count($argv) and isset($argv[1]) and $command = trim($argv[1]) ){

            // Init command
            array_shift($argv);

            if( 'init' != $command )
                // Configure
                $this->config();

            switch ( $command ) {

                case 'init':
                    // Install Candybar
                    return $this->install();

                case 'list':
                    // List available commands
                    return $this->showList();

                case 'version':
                case '--version':
                    // Display version
                    return $this->showVersion();

                case 'help':
                case '--help':
                    // Show cli/command help
                    return $this->showUsage(isset($argv[1]) ? $argv[1] : NULL);

                default:
                    // Execute command
                    return $this->execute($command, $argv);

            }

        }

        // Display usage
        return $this->showUsage();

    }

    /**
     * Executes given command
     * @param string $cmd
     * @param array $argv
     *
     * @throws \Exception
     */
    public function execute($cmd, $argv){

        if( in_array($cmd, array_keys($this->commands) ) ){

            $handler = $this->commands[$cmd];

            if( class_exists( $handler ) )

                //Initialize command object
                $handler = new $handler( $this->stdout );
                //$handler = new $handler();

            else

                // Class not found
               throw new \ReflectionException(
                   "Cannot find class {$handler} for command {$cmd} ... ."
               );

            // Run command with arguments
            if( is_a($handler, CommandInterface::class) )

                // Do-ya-thing
                ( isset($argv[1]) and '--help' == $argv[1] ) ?

                    // Show command help
                    $handler->showHelp() :

                    // Run command
                    $handler->run( $argv );

            else {

                // No face no name no number

                $classname = get_class($handler);
                $required  = CommandInterface::class;

                throw new \ReflectionException(
                    "Invalid handler provided `{$classname}`.
                        Command handlers should implement `$required` ."
                );

            }

        }
        else
            // Unrecognized command
            throw new UnknownCommandException($cmd);

    }

}