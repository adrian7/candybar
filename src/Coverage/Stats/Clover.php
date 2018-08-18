<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Coverage\Stats;

use DevLib\Candybar\Util;

class Clover implements StatisticsInterface{

    /**
     * Clover xml file
     * @var null|string
     */
    protected $filename = 'clover.xml';

    protected $numFiles;

    protected $linesOfCode;
    protected $linesOfComments;

    protected $methods;
    protected $coveredMethods;

    protected $conditionals;
    protected $coveredConditionals;

    protected $statements;
    protected $coveredStatements;

    protected $elements;
    protected $coveredElements;

    /**
     * Clover constructor.
     *
     * @param string $filename: clover formatted xml file path
     */
    public function __construct($filename) {

        $this->filename = $filename;

        if( is_readable($this->filename) )

            // Parse metrics from file
            $this->parseMetrics();

        else

            // Can't read filename
            throw new \InvalidArgumentException("Cannot read file at {$this->filename}... .");

    }

    /**
     * Parse metrics from file
     */
    public function parseMetrics(){

        $metrics = Util::getCloverXmlMetrics($this->filename);

        if( $metrics and isset($metrics['loc']) ); else
            throw new \InvalidArgumentException("Could not extract metrics from file {$this->filename}... .");

        //Init metrics
        $this->numFiles = data_get($metrics, 'files', 0);

        $this->linesOfCode    = data_get($metrics, 'ncloc', 0);
        $this->linesOfComments= ( data_get($metrics, 'loc', 0) - $this->linesOfCode );

        $this->methods        = data_get($metrics, 'methods', 0);
        $this->coveredMethods = data_get($metrics, 'coveredmethods', 0);

        $this->conditionals        = data_get($metrics, 'conditionals', 0);
        $this->coveredConditionals = data_get($metrics, 'coveredconditionals', 0);

        $this->statements        = data_get($metrics, 'statements', 0);
        $this->coveredStatements = data_get($metrics, 'coveredstatements', 0);

        $this->elements        = data_get($metrics, 'elements', 0);
        $this->coveredElements = data_get($metrics, 'coveredelements', 0);

    }

    public function linesOfCode(){
        return $this->linesOfCode;
    }

    public function linesOfComments(){
        return $this->linesOfComments;
    }

    public function elementsCount(){
        return $this->elements;
    }

    /**
     * @return mixed
     */
    public function coveredElementsCount(){
        return $this->coveredElements;
    }

    /**
     * Statements count
     * @return mixed
     */
    public function statementsCount(){
        return $this->statements;
    }

    /**
     * Covered statements count
     * @return mixed
     */
    public function coveredStatementsCount(){
        return $this->coveredStatements;
    }

    /**
     * Calculates code coverage percent
     *
     * @param string|array $metrics : metrics to calculate percentage for
     * @param bool $round : round result to integer
     *
     * @return float|int
     */
    public function coveragePercent($metrics='all', $round=FALSE){

        if( 'all' == $metrics )
            $metrics = ['elements', 'statements', 'methods', 'conditionals'];

        $values = [];

        foreach ($metrics as $metric){

            if( ! property_exists($this, $metric) )
                // Metric not available
                throw new \InvalidArgumentException("Metric {$metric} is not available... ");

            $covered  = ('covered' . ucfirst($metric) );

            if( 0 == $this->{$covered} )
                // uncovered 100%
                $values[] = 0;

            elseif( 0 < $this->{$metric} )
                // Percentage uncovered
                $values[] = floatval( $this->{$covered} / $this->{$metric} );

        }

        // Calculate percentage covered
        $percentage = ( $s = array_sum($values) ) ?
            ( $s / count($values) ) * 100 :
            0;

        return $round ?
            Util::round($percentage, 0) :
            Util::round($percentage, 2);

    }

    /**
     * Calculate coverage of elements
     * @return float|int
     */
    public function coverageOfElementsPercent(){
        return $this->coveragePercent(['elements']);
    }

    /**
     * Calculate coverage of statements
     * @return float|int
     */
    public function coverageOfStatementsPercent(){
        return $this->coveragePercent(['statements']);
    }

    /**
     * Calculate percentage for lines of comments over lines of executable code
     *
     * @param bool $round
     *
     * @return float|int
     */
    public function commentsByLocPercent($round=FALSE){

        $percentage = ( $this->linesOfComments / $this->linesOfCode ) * 100;

        return $round ?
            Util::round($percentage, 0) : Util::round($percentage, 2);

    }
}