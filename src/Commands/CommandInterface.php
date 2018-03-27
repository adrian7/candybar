<?php
/**
 * Candybar - CLI Command Interface
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

interface CommandInterface{

    public function showHelp();

    public function run(array $argv);

    public function exitWithError(\Exception $e);

}