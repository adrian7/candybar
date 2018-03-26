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
    protected $arguments = [

        //TODO move this to subclass

        //An argument with default value and description
        'one' => [
            'default'     => 'value',
            'description' => 'Just a placeholder argument'
        ],

        //An argument without description
        'two' => 'defaultValue'

    ];

    /**
     * Available options (long options only)
     * @var
     */
    protected $options = [

        //TODO move that within subclass

        //Option with default value and description
        'key' => [
            'default'     => NULL,
            'description' => 'The option description'
        ],

        //Option without a description
        'secret' => NULL,

        //Boolean option (switch)
        'erase' => FALSE

    ];

    /**
     * Indicates if the version string was printed
     * @var bool
     */
    private $versionStringPrinted = FALSE;

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

        //Setup long options

        if( count($this->options) )
            foreach ($this->options as $name=>$spec)
                $this->longOpts["--{$name}="] = NULL;

    }

    /**
     * Set input
     *
     * @param $name
     * @param $value
     * @param array $input
     */
    private function setInput($name, $value, &$input=[]){

        if( isset($input[$name]) )
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
            "Missing spec. for input key {$name}... ." :
            $errorMsg;

        if( isset($input[$name]) ){

            //Input key available

            $value = is_array($input[$name]) ?
                data_get(
                    'value',
                    $input[$name],
                    data_get($input[$name], 'default')
                ):
                $input[$name];

            //Return
            return $value;
        }

        throw new \InvalidArgumentException($errorMsg);

    }

    /**
     * Displays help
     */
    public function showHelp() {

        //TODO generate help

        $argv     = $_SERVER['argv'];
        $filename = basename( array_shift($argv) );

        print <<<EOT
        
Command help instructions go here... .


EOT;

    }

    /**
     * Extracts CLI arguments
     * @param array $argv
     */
    public function parseInput( array $argv ) {

        $options   = array_keys($this->options);
        $arguments = array_keys($this->arguments);

        //Handle options
        try {

            $this->opts = Getopt::getopt(
                $argv,
                '',
                \array_keys($this->longOpts)
            );

        } catch (Exception $t) {
            $this->exitWithErrorMessage( $t->getMessage() );
        }

        if( count($this->opts[0]) )
            foreach ($this->opts[0] as $option) {

            //0 -> is the option string --option
            //1 -> is the value

            $name = trim($option[0], '-');

            if( in_array($name, $options) )
                $this->setOption($name, $option[1]);

            if( 'help' == $name )
                $this->showHelp( TRUE );

        }

        dd( $this->options );

        //Handle arguments
        if( 1 < count($argv) ){
            //TODO...
            dd($argv);
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

    protected function exitWithError(\Exception $e) {

        if( method_exists($this, 'showVersion') )
            //Show version if available
            $this->showVersion(TRUE);

        //TODO support for PSR3
        print ( PHP_EOL . "Error: " . $e->getMessage() . PHP_EOL . PHP_EOL );

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