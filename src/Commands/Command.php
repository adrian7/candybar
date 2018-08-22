<?php
/**
 * Candybar - Command class
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use PHPUnit\Util\Getopt;
use DevLib\Candybar\Util;

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
     * The standard output STDOUR/ERR channel
     * @var string
     */
    protected $stdout = 'standard';

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
     * @param string $stdout
     */
    public function __construct($stdout=Util::STANDARD_OUTPUT) {

        if( empty($stdout) )
            throw new \InvalidArgumentException(
                "Stdout should point to a file or `standard`... ."
            );

        //Set output channel
        $this->stdout = $stdout;

        //Setup defaults
        $this->setup();

    }

    /**
     * Init command defaults
     */
    private function setup(){

        // Setup long options for Getopt

        if( count($this->options) )
            foreach ($this->options as $name=>$spec){

                if( is_bool( $this->option($name) ) );
                    // Is boolean option
                else
                    // Requires an argument
                    $name.='=';

                $this->longOpts["{$name}"] = NULL;

            }

        // Append --help option
        $this->longOpts["help"] = NULL;

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

            // Input key available

            $value = is_array($input[$name]) ?
                data_get(
                    $input[$name],
                    'value',
                    data_get($input[$name], 'default')
                ):
                $input[$name];

            // Return
            return $value;

        }

        throw new \InvalidArgumentException($errorMsg);

    }

    /**
     * Displays command help
     *
     * @param bool $exit
     */
    public function showHelp($exit=TRUE) {

        $argv    = $_SERVER['argv'];
        $script  = basename( array_shift($argv) );

        if( $command = array_shift($argv) and 'help' == $command )
            // Querying help about a command
            $command = array_shift($argv);

        $hasArguments = count($this->arguments) ? '{arguments}' : '';
        $hasOptions   = count($this->options)   ? '{--options}' : '';

        $this->line(" {$command}");
        $this->eol("   " . $this->description);
        $this->line(" Usage:   {$script} {$command} {$hasArguments} {$hasOptions}");

        if( count($this->arguments) ){

            // Display list of command arguments

            $this->line(" Arguments: " . PHP_EOL);

            foreach ($this->arguments as $arg=>$cfg)
                $this->eol(
                    sprintf("%-16s", "  <{$arg}>") .
                    ( is_array($cfg) ? data_get($cfg, 'description') : '' )
                );

        }

        if( count($this->options) ){

            // Display list of command options
            $this->line(" Options: " . PHP_EOL);

            foreach ($this->options as $opt=>$cfg){

                $optstring =
                    "  --{$opt}" .
                    ( is_bool($this->option($opt) ) ? '' : "=<value> " );

                $this->eol(
                    sprintf("%-24s", $optstring ).
                    ( is_array($cfg) ? data_get($cfg, 'description') : '' )
                );

            }

        }

        $this->eol();

        //TODO add support for exit
        //if( $exit )
            //Exit after showing help
        //    exit( self::SUCCESS_EXIT );

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
     * Parse command option; All options are treated as optional
     *
     * @param array $options
     * @param array $longOptions
     * @return void
     */
    public function parseOptions($options=[], $longOptions=[]){

        $commandOptions = array_keys($this->options);
        $inputOptions   = [];

        if( isset($options[0][0]) and '--help' == $options[0][0] ) {

            // Show help
            $this->showHelp();

            return;

        }

        // Parse given options
        if( count($options) )

            foreach ($options as $option) {

                list($name, $value) = $option;

                $inputOptions[] = $name;

                // Extract option name
                $name = trim($name, '-');

                // Check for chained empty options e.g. --opt --chained-opt
                $cleanValue              = trim(trim($value), '-');
                $valueOverriddenByOption = (
                    strpos($value, '--') !== FALSE
                    and (
                        in_array($cleanValue, $longOptions)
                            or
                        in_array( "{$cleanValue}=", $longOptions)
                    )
                );

                if( $valueOverriddenByOption )
                    // An invalid chain of options, e.g. --opt= --opt
                    throw new \InvalidArgumentException(
                        sprintf("Option %s requires a value... .", "--{$name}")
                    );

                if( in_array($name, $commandOptions) ){

                    if(
                        empty($value)
                            and
                        array_key_exists("{$name}=", $this->longOpts)
                    )
                        // The option requires a value
                        throw new \InvalidArgumentException(
                            sprintf("Option %s requires a value... .", "--{$name}")
                        );

                    else
                        // Set option
                        $this->setOption($name, empty($value) ? TRUE : $value );

                }
                else
                    // Unrecognized option
                    throw new \InvalidArgumentException(
                        sprintf("Unrecognized option %s ... .", "--{$name}")
                    );

            }

        // Check the required options
        foreach ($this->options as $name=>$spec)
            if(
                ( isset($spec['required']) and $spec['required'] )
                    and
                ! in_array($name, $inputOptions)
            )
                throw new \InvalidArgumentException("Option --{$name} is required... .");

    }

    /**
     * Parse command arguments
     * @param $arguments
     */
    public function parseArguments($arguments){

        $commandArguments = array_keys($this->arguments);

        // Parse arguments
        if( count($arguments) ){

            if( count($commandArguments) < count($arguments) )
                // Invalid number of arguments
                throw new \InvalidArgumentException(
                    sprintf(
                        "Command supports only %s argument%s, %s given ... .",
                        count($commandArguments),
                        ( count($commandArguments) > 1 ? 's' : ''),
                        count($arguments)
                    )
                );

            foreach ($arguments as $value)
                // Set arguments
                $this->setArgument(
                    array_shift($commandArguments),
                    $value
                );

        }

        // Check required arguments
        $required = 0;

        foreach ($this->arguments as $name=>$spec)
            if( isset($spec['required']) and $spec['required'] )
                $required++;

        if( $required > count($arguments) )
            throw new \InvalidArgumentException(
                sprintf(
                    "Command requires %s argument%s, %s given.",
                    $required,
                    ( $required > 1 ? 's' : ''),
                    count($arguments)
                )
            );

    }

    /**
     * Command run
     * @param array $argv
     *
     * @return int|void
     * @throws \Exception
     */
    public function run(array $argv) {

        // Parse input
        $this->parseInput($argv);

        // Handle command
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
    public function argument($name){

        if( ! is_string($name) )
            throw new \InvalidArgumentException("Argument name should be a string... .");


        return $this->getInput($name, $this->arguments);

    }

    /**
     * Sets an option
     * @param string $name
     * @param mixed $value
     */
    protected function setOption($name, $value){

        $this->setInput($name, $value, $this->options);

    }

    /**
     * Retrieve option value
     * @param string $name
     *
     * @return mixed
     */
    public function option($name){

        if( ! is_string($name) )
            throw new \InvalidArgumentException("Option name should be a string... .");

        return $this->getInput($name, $this->options);

    }

    /**
     * Display warning message
     * @param string $message
     */
    public function warn($message){
        $this->line("\033[1;33m(w) {$message}\033[0m");
    }

    /**
     * Display informational message
     * @param string $message
     */
    public function info($message){
        $this->line("\033[1;36m(i) {$message}\033[0m");
    }

    /**
     * Display a success message
     * @param string $message
     */
    public function success($message){
        $this->line("\033[1;32m(i) {$message}\033[0m");
    }

    /**
     * Prints a line
     *
     * @param string $message
     */
    public function line($message=''){
        Util::out (PHP_EOL . strval($message) . PHP_EOL, $this->stdout);
    }

    /**
     * Prints message followed by End-of-line
     *
     * @param string $message
     */
    public function eol($message=''){
        Util::out($message . PHP_EOL, $this->stdout);
    }

    /**
     * @param \Exception $e
     *
     * @throws \Exception
     */
    public function exitWithError(\Exception $e) {

        if( method_exists($this, 'showVersion') )
            // Show version if available
            $this->showVersion(TRUE);

        Util::out (
            sprintf(
                PHP_EOL . "\033[1;31m%s \033[0m" . PHP_EOL . PHP_EOL,
                "Error: " . $e->getMessage()
            ),
            $this->stdout
        );

        Util::out(
            sprintf("\033[1;33mStack trace: \033[0m" . PHP_EOL),
            $this->stdout
        );

        // Throw error
        throw $e;

    }

    /**
     * Command handle
     */
    public function handle(){
        // TODO add better handle with support
        Util::out(
            "This command doesn't implement a handle function... .",
            $this->stdout
        );
    }

}