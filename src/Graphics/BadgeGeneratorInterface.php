<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Graphics;

interface BadgeGeneratorInterface{

    /**
     * Generates a badge
     *
     * @param string $text
     * @param string $value
     * @param string $color
     * @param string $style
     *
     * @return resource
     */
    public function make($text, $value, $color, $style);

    /**
     * Save generated badge as a file
     *
     * @param resource $resource
     * @param string $filename
     *
     * @return string
     */
    public function save($resource, $filename);

}