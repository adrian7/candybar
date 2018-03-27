<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use PHPUnit\Util\Getopt;
use PHPUnit\Framework\Exception;

abstract class Command implements CommandInterface {

    /**
     * Normal exit - 0
     */
    const SUCCESS_EXIT = 0;

    /**
     * Abnormal exit - 2
     */
    const ABNORMAL_EXIT = 2;

    /**
     * List of cli options
     * @var array
     */
    private $opts = [];

    /**
     * Command long options
     * @var array
     */
    private $longOpts = [];

    /**
     * Command description
     * @var string
     */
    protected $description;

    /**
     * Provided list of parameters
     * @var array
     */
    protected $arguments = [];

    /**
     * Available options (long options only)
     * @var
     */
    protected $options = [];

    /**
     * Command constructor.
     *
     * @param null $argv
     */
    public function __construct($argv=NULL) {

        //Setup defaults
        $this->setup();

    }

    /**
     * Init command defaults
     */
    private function setup(){

        //Setup long options for Getopt

        if( count($this->options) )
            foreach ($this->options as $name=>$spec){

                if( is_bool( $this->option($name) ) );
                    //Is boolean option
                else
                    //Requires an argument
                    $name.='=';

                $this->longOpts["{$name}"] = NULL;

            }

    }

    /**
     * Set input
     *
     * @param $name
     * @param $value
     * @param array $input
     */
    private function setInput($name, $value, &$input=[]){

        if( array_key_exists($name, $input) )
            $input[$name] = is_array($input[$name]) ?
                [
                    'value'         => $value,
                    'default'       => data_get($input[$name], 'default'),
                    'description'   => data_get($input[$name], 'description')
                ] :
                $value;

    }

    /**
     * Retrieve input
     *
     * @param $name
     * @param $input
     * @param null $errorMsg
     *
     * @return mixed
     */
    private function getInput($name, $input, $errorMsg=NULL){

        $errorMsg = empty($errorMsg) ?
            "Missing spec. for input key <{$name}> ... ." :
            $errorMsg;

        if( array_key_exists($name, $input) ){

            //Input key available

            $value = is_array($input[$name]) ?
                data_get(
                    $input[$name],
                    'value',
                    data_get($input[$name], 'default')
                ):
                $input[$name];

            //Return
            return $value;

        }

        throw new \InvalidArgumentException($errorMsg);

    }

    /**
     * Displays command help
     */
    public function showHelp() {

        $argv    = $_SERVER['argv'];

        $script  = basename( array_shift($argv) );

        if( $command = array_shift($argv) and 'help' == $command )
            //Querying help about a command
            $command = array_shift($argv);

        $hasArguments = count($this->arguments) ? '{arguments}' : '';
        $hasOptions   = count($this->options)   ? '{--options}' : '';

        $this->line(" {$command}");
        $this->eol("   " . $this->description);
        $this->line(" Usage:   {$script} {$command} {$hasArguments} {$hasOptions}");

        if( count($this->arguments) ){

            //Display list of command arguments

            $this->line(" Arguments: " . PHP_EOL);

            foreach ($this->arguments as $arg=>$cfg)
                $this->eol(
                    "  <{$arg}>    " .
                    ( is_array($cfg) ? data_get($cfg, 'description') : '' )
                );

        }

        if( count($this->options) ){

            //Display list of command options
            $this->line(" Options: " . PHP_EOL);

            foreach ($this->options as $opt=>$cfg)
                $this->eol(
                    "  --{$opt}" .
                    ( is_bool($this->option($opt) ) ? '' : "=<value>" ) .
                    ( is_array($cfg) ? data_get($cfg, 'description') : '' )
                );

        }

        $this->eol();

    }

    /**
     * Extracts CLI arguments
     * @param array $argv
     *
     * @throws \Exception
     */
    public function parseInput( array $argv ) {

        $longOptions = \array_keys($this->longOpts);

        //Handle options
        list($options, $arguments) = Getopt::getopt(
            $argv,
            '',
            $longOptions
        );

        $this->parseOptions($options, $longOptions);

        $this->parseArguments($arguments);

    }

    /**
     * Parse command option
     *
     * @param array $options
     * @param array $longOptions
     */
    public function parseOptions($options=[], $longOptions=[]){

        $commandOptions = array_keys($this->options);

        //Parse options
        if( count($options) )

            foreach ($options as $option) {

                list($name, $value) = $option;

                //Extract option name
                $name = trim($name, '-');

                //Check for chained empty options
                $cleanValue              = trim(trim($value), '-');
                $valueOverriddenByOption = (
                    strpos($value, '--') !== FALSE
                    and (
                        in_array($cleanValue, $longOptions)
                        or
                        in_array( "{$cleanValue}=", $longOptions)
                    )
                );

                if( $valueOverriddenByOption)
                    throw new \InvalidArgumentException(
                        sprintf("Option %s requires a value... .", "--{$name}")
                    );

                if( in_array($name, $commandOptions) ){

                    if(
                        empty($value)
                        and
                        array_key_exists("{$name}=", $this->longOpts)
                    )
                        //The option requires a value
                        throw new \InvalidArgumentException(
                            sprintf("Option %s requires a value... .", "--{$name}")
                        );

                    else
                        //Set option
                        $this->setOption($name, empty($value) ? TRUE : $value );

                }
                else
                    //Unrecognized option
                    throw new \InvalidArgumentException(
                        sprintf("Unrecognized option %s ... .", "--{$name}")
                    );

                if( 'help' == $name )
                    $this->showHelp();

            }

    }

    /**
     * Parse command arguments
     * @param $arguments
     */
    public function parseArguments($arguments){

        $commandArguments = array_keys($this->arguments);

        //Parse arguments
        if( count($arguments) ){

            if( count($commandArguments) < count($arguments) )
                //Invalid number of arguments
                throw new \InvalidArgumentException(
                    sprintf(
                        "Command supports only %s arguments, %s given ... .",
                        count($commandArguments),
                        count($arguments)
                    )
                );

            foreach ($arguments as $value)
                $this->setArgument(
                    array_shift($commandArguments),
                    $value
                );

        }

    }

    /**
     * Command run
     * @param array $argv
     * @param bool $exit
     *
     * @return int|void
     */
    public function run(array $argv){

        //Parse input
        $this->parseInput($argv);

        //Handle command
        $this->handle();

    }

    /**
     * Set an argument's value
     * @param string $name
     * @param mixed $value
     */
    protected function setArgument($name, $value){
        $this->setInput($name, $value, $this->arguments);
    }

    /**
     * Retrieve an argument's value
     * @param string $name
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function argument($name){

        if( ! is_string($name) )
            throw new \InvalidArgumentException("Argument name should be a string... .");


        return $this->getInput($name, $this->arguments);

    }

    protected function setOption($name, $value){

        $this->setInput($name, $value, $this->options);

    }

    /**
     * Retrieve option value
     * @param $name
     *
     * @return mixed
     */
    protected function option($name){

        if( ! is_string($name) )
            throw new \InvalidArgumentException("Option name should be a string... .");

        return $this->getInput($name, $this->options);

    }

    public function warn($message){
        $this->line("(w) {$message}");
    }

    public function info($message){
        $this->line("(i) {$message}");
    }

    public function success($message){
        $this->line("(i) {$message}");
    }

    /**
     * Prints a line
     *
     * @param string $message
     * @param string $style
     */
    public function line($message='', $style='default'){
        print (PHP_EOL . strval($message) . PHP_EOL);
        //TODO add support for style
    }

    /**
     * Prints message followed by End-of-line
     *
     * @param string $message
     * @param string $style
     */
    public function eol($message='', $style='default'){
        print ($message . PHP_EOL);
        //TODO add support for style
    }

    /**
     * @param \Exception $e
     *
     * @throws \Exception
     */
    public function exitWithError(\Exception $e) {

        if( method_exists($this, 'showVersion') )
            //Show version if available
            $this->showVersion(TRUE);

        //TODO support for PSR3
        print ( PHP_EOL . " Error: " . $e->getMessage() . PHP_EOL . PHP_EOL );
        print (" Stack trace: " . PHP_EOL);

        //Throw error
        throw $e;

        exit(self::ABNORMAL_EXIT);

    }

    /**
     * Command handle
     */
    public function handle(){
        //TODO add better handle with support and such
        print "This command doesn't implement a handle function... .";
    }
}