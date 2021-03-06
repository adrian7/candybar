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
     * CLI output file
     * @var string
     */
    protected static $output = ( __DIR__ . '/data/cli.out' );

    /**
     * Backup of cwd
     * @var string
     */
    protected static $backupCWD;

    public function tearDown() {

        // Cleanup output after test
        if( file_exists( self::$output ) )
            file_put_contents(self::$output, '');

        if( getcwd() != self::$backupCWD )
            // Restore CWD
            chdir(self::$backupCWD);

        // Drop all cached configs
        \DevLib\Candybar\Util::dropCaches();

    }

    /**
     * @param bool $force
     *
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     * @throws \DevLib\Candybar\Exceptions\InvalidConfigurationException
     */
    public function setUp($force=FALSE) {

        // Backup CWD
        self::$backupCWD = getcwd();

        if( $force or empty(self::$runner) )
            self::$runner = new \DevLib\Candybar\Cli( self::$output );

        self::$runner->config();

    }

    protected function getOutput(){
        return file_get_contents(self::$output);
    }

    /**
     * Runs a cli command and looks up keywords in the output
     * @param $command
     * @param array $args
     * @param array $expectKeywords
     *
     * @throws Exception
     */
    protected function execute(
        $command,
        $args=[],
        $expectKeywords=[]
    ){

        $args = array_merge([
            'bin/executable', // Script name
            trim($command),   // Command
        ], $args);

        // Set args
        $_SERVER['argv'] = $args;

        // Handle arguments
        self::$runner->run($args);

        if( $expectKeywords ){

            $output = $this->getOutput();

            //Do we get some keywords?
            foreach ($expectKeywords as $keyword)
                $this->assertContains($keyword, $output);

        }

    }

    /**
     * @param $command
     * @param array $args
     *
     * @throws Exception
     */
    protected function verbose($command, $args=[]){
        $this->execute($command, $args);
    }

    /**
     * Test command and capture output
     *
     * @param $command
     * @param array $args
     * @param array $expectKeywords
     *
     * @throws Exception
     */
    protected function silent($command, $args=[], $expectKeywords=[]){
        $this->execute($command, $args, $expectKeywords);
    }

}