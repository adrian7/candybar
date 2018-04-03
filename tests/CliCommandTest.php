<?php
/**
 * Candybar - CLI Command test helper
 * @author adrian7
 * @version 1.0
 */

abstract class CliCommandTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\DevLib\Candybar\Commands\Command
     */
    protected static $runner = NULL;

    /**
     * @param bool $force
     *
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     * @throws \DevLib\Candybar\Exceptions\InvalidConfigurationException
     */
    public function setUp($force=FALSE) {

        if( $force or empty(self::$runner) )
            self::$runner = new \DevLib\Candybar\Cli();

        self::$runner->config();

    }

    /**
     * Runs a cli command and looks up keywords in the output
     *
     * @param string $command
     * @param array $args
     * @param array $expectKeywords
     * @param bool $captureOutput
     *
     * @return string|bool
     */
    protected function execute(
        $command,
        $args=[],
        $expectKeywords=[],
        $captureOutput=TRUE
    ){

        $args = array_merge([
            'bin/executable', //Script name
            trim($command),   //Command
        ], $args);

        //Turn on output buffering
        if( $captureOutput )
            ob_start();

        //Handle arguments
        self::$runner->run($args);

        if( $captureOutput ){

            //Retrieve output
            $result = ob_get_contents();

            //Turn of output buffering (assume it was turned off)
            ob_end_clean();

            //Do we get some keywords?
            foreach ($expectKeywords as $keyword)
                $this->assertContains($keyword, $result);

            //Return command output
            return $result;

        }

        //Command executed successfully
        return TRUE;

    }

    /**
     * Run command
     *
     * @param string $command
     * @param array $args
     */
    protected function verbose($command, $args=[]){
        $this->execute($command, $args, [], FALSE);
    }

    /**
     * Test command and capture output
     *
     * @param string $command
     * @param array $args
     * @param array $expectKeywords
     */
    protected function silent($command, $args=[], $expectKeywords=[]){
        $this->execute($command, $args, $expectKeywords, TRUE);
    }

}