<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CliTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\DevLib\Candybar\Cli
     */
    protected static $runner = NULL;

    /**
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     * @throws \DevLib\Candybar\Exceptions\InvalidConfigurationException
     */
    public function setUp() {

        if( empty(self::$runner) )
            self::$runner = new \DevLib\Candybar\Cli();

        self::$runner->config();

    }

    public function cliCommandTest($command, $expectKeywords=[]){

        $args = [
            'bin/executable', //Script name

            //Args
            trim($command)

        ];

        //Turn on output buffering
        ob_start();

        //Handle arguments
        self::$runner->run($args);

        //Retrieve output
        $result = ob_get_contents();

        //Turn of output buffering (assume it was turned off)
        ob_end_clean();

        //Do we get some keywords?
        foreach ($expectKeywords as $keyword)
            $this->assertContains($keyword, $result);

    }


    public function testHelpCommand(){

        $this->cliCommandTest('help', ['help', 'Usage', \DevLib\Candybar\Cli::VERSION]);

    }

    public function testListCommand(){

        //Test list command with default commands
        $this->cliCommandTest('list', [
            'coverage:style',
            'badge:coverage',
            'example:command'
        ]);

    }

}