<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Coverage\Stats;

interface StatisticsInterface{

    public function parseMetrics();

    public function linesOfCode();

    public function linesOfComments();

    public function elementsCount();

    public function coveredElementsCount();

    public function statementsCount();

    public function coveredStatementsCount();

    public function coveragePercent();

    public function commentsByLocPercent();

}