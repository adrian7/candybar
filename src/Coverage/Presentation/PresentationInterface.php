<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Coverage\Presentation;

interface PresentationInterface{

    public function getIndexPath();

    public function getCssPath($file);

    public function getJsPath($file);

    public function setStyle($name);

    public function createStyle($source);

    public function addJsScript($contents);

    public function setTheme($name);

    public function createTheme($source);

}