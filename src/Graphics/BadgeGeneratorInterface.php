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
     * @param string $subject
     * @param string $value
     * @param string $color
     * @param string $style
     *
     * @return resource
     */
    public static function make($subject, $value, $color, $style);

    /**
     * Save generated badge as a file
     *
     * @param string $filename
     *
     * @return string
     */
    public function save($filename);

}