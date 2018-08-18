<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CoverageCloverTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\DevLib\Candybar\Coverage\Stats\Clover
     */
    protected static $clover = NULL;

    public function setUp() {

        $path = ( __DIR__ . '/data/coverage-clover-sample.xml' );

        if( empty(self::$clover) )
            self::$clover = new \DevLib\Candybar\Coverage\Stats\Clover($path);

    }

    public function tearDown() {

        // Drop cached config and metrics
        \DevLib\Candybar\Util::dropCaches();

    }

    public function testParseMetrics(){

        $this->assertEquals(1796, self::$clover->linesOfCode());
        $this->assertEquals(2253- 1796, self::$clover->linesOfComments());
        $this->assertEquals(399, self::$clover->elementsCount());
        $this->assertEquals(397, self::$clover->statementsCount());
        $this->assertEquals(60, self::$clover->coveredElementsCount());
        $this->assertEquals(60, self::$clover->coveredStatementsCount());

    }

    public function testCoveragePercent(){

        $metrics = ['elements', 'statements'];

        // Coverage of elements
        $this->assertEquals(
            DevLib\Candybar\Util::round((60*100)/399, 2),
            self::$clover->coverageOfElementsPercent()
        );

        // Coverage of statements
        $this->assertEquals(
            DevLib\Candybar\Util::round((60*100)/397, 2),
            self::$clover->coverageOfStatementsPercent()
        );

        // Coverage of statements (similar to phpunit's)
        $this->assertEquals(
            15,
            self::$clover->coveragePercent(['statements'], TRUE)
        );

        // Coverage of two metrics
        $this->assertEquals(15,
            self::$clover->coveragePercent($metrics, TRUE)
        );

        // Global coverage
        $this->assertEquals(
            8,
            self::$clover->coveragePercent('all', TRUE)
        );

    }

    public function testCommentsByLocPercent(){

        $this->assertEquals(
            25,
            self::$clover->commentsByLocPercent(TRUE)
        );

    }

    public function testFailsWhenFileNotReadable(){

        $this->expectException(\InvalidArgumentException::class);

        new \DevLib\Candybar\Coverage\Stats\Clover('/missing/clover.xml');

    }

    public function testFailsWhenFileNotValid(){

        $this->expectException(\InvalidArgumentException::class);

        new \DevLib\Candybar\Coverage\Stats\Clover(
            __DIR__ . '/data/coverage-clover-sample-invalid.xml'
        );

    }

    public function testFailsWhenMetricNotAvailable(){

        $this->expectException(\InvalidArgumentException::class);

        self::$clover->coveragePercent(['elements', 'unavailable']);

    }

}