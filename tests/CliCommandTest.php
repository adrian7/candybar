<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

abstract class CliCommandTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\DevLib\Candybar\Commands\Command
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

    /**
     * Runs a cli command and looks up keywords in the output
     *
     * @param string $command
     * @param array $args
     * @param array $expectKeywords
     */
    public function runCommandTest($command, $args=[], $expectKeywords=[]){

        $args = array_merge([
            'bin/executable', //Script name
            trim($command),   //Command
        ], $args);

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

}