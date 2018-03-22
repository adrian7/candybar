<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

class CoverageCloverTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var null|\PHPUnit\Candies\Coverage\Clover
     */
    protected static $clover = NULL;

    public function setUp() {

        $path = ( __DIR__ . '/data/coverage-clover.xml' );

        if( empty(self::$clover) )
            self::$clover = new \PHPUnit\Candies\Coverage\Clover($path);

    }

    public function testParseMetrics(){

        $this->assertEquals(1796, self::$clover->linesOfCode());
        $this->assertEquals(397, self::$clover->statementsCount());
        $this->assertEquals(60, self::$clover->coveredElementsCount());

    }

    public function testCoveragePercent(){

        $metrics = ['elements', 'statements'];

        //Coverage of statements (similar to phpunit's)
        $this->assertEquals(
            15,
            self::$clover->coveragePercent(['statements'], TRUE)
        );

        //Coverage of two metrics
        $this->assertEquals(15,
            self::$clover->coveragePercent($metrics, TRUE)
        );

        //Global coverage
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
}