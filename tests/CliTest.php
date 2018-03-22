<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CliTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\PHPUnit\Candies\Cli
     */
    protected static $runner = NULL;

    public function setUp() {

        if( empty(self::$runner) )
            self::$runner = new \PHPUnit\Candies\Cli();

    }

    public function testShortOptions(){

        $args = [
            'bin/executable', //Script name

            //Args
            '-c configuration.xml',
            '-o output/folder',
            '-s mystyle',
            '-b coverage,phpstan',

        ];

        //Handle arguments
        self::$runner->handleArguments($args);

        $received = self::$runner->getArguments();

        //Assert configuration
        $this->assertEquals('configuration.xml', $received['configuration']);
        $this->assertEquals('mystyle', $received['style']);
        $this->assertEquals(['coverage', 'phpstan'], $received['badges']);
        $this->assertEquals('output/folder', $received['output']);

    }

    public function testLongOptions(){

        $args = [
            'bin/executable', //Script name

            //Args
            '--configuration=configuration-long.xml',
            '--output=phpunit/output/folder',
            '--style=customstyle',
            '--badges=coverage,someotherbadge',
            '--s3-key=AWSTESTKEY',
            '--s3-secret=AWSTESTSECRET',

        ];

        //Handle arguments
        self::$runner->handleArguments($args);

        $received = self::$runner->getArguments();

        //Assert configuration
        $this->assertEquals('configuration-long.xml', $received['configuration']);
        $this->assertEquals('customstyle', $received['style']);
        $this->assertEquals(['coverage', 'someotherbadge'], $received['badges']);
        $this->assertEquals('phpunit/output/folder', $received['output']);
        $this->assertEquals('AWSTESTKEY', $received['s3_key']);
        $this->assertEquals('AWSTESTSECRET', $received['s3_secret']);

    }

}