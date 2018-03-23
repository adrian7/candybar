<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Coverage\Coverage;
use DevLib\Candybar\Coverage\Stats\Clover;
use PHPUnit\Util\Getopt;
use PHPUnit\Framework\Exception;

class Cli {

    /**
     * Normal exit - 0
     */
    const SUCCESS_EXIT = 0;

    /**
     * Abnormal exit - 2
     */
    const ABNORMAL_EXIT = 2;

    /**
     * Default phpunit config
     */
    const PHPUNIT_DEFAULT_CFG = 'phpunit.xml';

    /**
     * List of cli options
     * @var array
     */
    protected $options;

    /**
     * List of arguments
     * @var array
     */
    protected $arguments = [
        'badges'        => 'coverage',
        'configuration' => self::PHPUNIT_DEFAULT_CFG,
        'output'        => NULL,
        's3_key'        => NULL,
        's3_secret'     => NULL,
        'style'         => 'default',
        //TODO add phpstan options
        //TODO add rules for pushing code to prod
    ];

    /**
     * Long options
     * @var array
     */
    protected $longOptions = [
        'badges='          => NULL,
        'configuration='   => NULL,
        'help'             => NULL,
        'output='          => NULL,
        's3-key='          => NULL,
        's3-secret='       => NULL,
        'style='           => NULL
    ];

    /**
     * @param bool $exit
     *
     * @return mixed
     */
    public static function main($exit = TRUE) {

        $command = new static;

        return $command->run($_SERVER['argv'], $exit);

    }

    /**
     * Displays help
     */
    protected function showHelp() {

        $argv     = $_SERVER['argv'];
        $filename = basename( array_shift($argv) );

        print <<<EOT
        
Usage: {$filename} [options]

Options: 

 -c <file>      path to PHPunit's config file; E.g. -c phpunit-config.xml 
 -o <dir>       path to PHPUnit's output folder; E.g. -o tests/output
 -s <style>     name of css style to apply to code coverage html report


EOT;

    }

    /**
     * Extracts CLI arguments
     * @param array $argv
     */
    public function handleArguments( array $argv ) {

        try {

            $this->options = Getopt::getopt(
                $argv,
                's:c:b:o:h',
                \array_keys($this->longOptions)
            );

        } catch (Exception $t) {
            $this->exitWithErrorMessage( $t->getMessage() );
        }

        foreach ($this->options[0] as $option) {
            switch ($option[0]) {

                //Set style to apply
                case 's':
                case '--style':
                    $this->arguments['style'] = $option[1] ?
                        strtolower( trim($option[1]) ) :
                        'default';

                    break;

                //Set badges to generate
                case 'b':
                case '--badges':
                    $this->arguments['badges'] = $option[1] ?
                        @explode(',', trim($option[1])) :
                        ['coverage'];

                    break;

                //Set S3 key
                case '--s3-key':
                    $this->arguments['s3_key'] = trim($option[1]);

                    break;

                //Set S3 secret access key
                case '--s3-secret':
                    $this->arguments['s3_secret'] = trim($option[1]);

                    break;

                //Set configuration file
                case 'c':
                case '--configuration':
                    $this->arguments['configuration'] = trim($option[1]) ?:
                        self::PHPUNIT_DEFAULT_CFG;

                    break;

                //Set PHPUnit's output folder
                case 'o':
                case '--output':
                    $this->arguments['output'] = trim($option[1]);

                    break;

                //Display help
                case 'h':
                case '--help':
                    $this->showHelp();
                    exit(self::SUCCESS_EXIT);

                    break;

            }

        }

    }

    /**
     * Command run
     * @param array $argv
     * @param bool $exit
     *
     * @return int|void
     */
    public function run(array $argv, $exit=TRUE){

        $this->handleArguments($argv);

        //Generate code coverage stats

        //Init repository defaults
        //! Repository::registered('Coverage/Statistics');

        //Resolve objects from repository
        //Repository::resolve('Coverage/Stats');
    }

    public function getArguments(){
        return $this->arguments;
    }

    private function exitWithErrorMessage($message) {

        //$this->printVersionString();

        print $message . PHP_EOL;

        exit(self::ABNORMAL_EXIT);

    }

    public function getCodeCoverageInfo(){
        //TODO...
    }

}