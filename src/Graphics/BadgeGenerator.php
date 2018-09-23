<?php
/**
 * Candybar - Badge Generator helper
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Graphics;

use PUGX\Poser\Image;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;
use PUGX\Poser\Render\SvgFlatRender;
use PUGX\Poser\Render\RenderInterface;
use PUGX\Poser\Render\SvgFlatSquareRender;

class BadgeGenerator implements BadgeGeneratorInterface{

    /**
     * Available styles
     */
    const STYLE_DEFAULT     = 'svg';

    const STYLE_FLAT        = 'flat';
    const STYLE_PLASTIC     = 'plastic';
    const STYLE_FLAT_SQUARE = 'flat-square';


    /**
     * Generated badge image
     * @var null|Image
     */
    protected $badge;

    /**
     * Badge renderer
     * @var RenderInterface
     */
    protected $renderer;

    /**
     * Badge style
     * @var string
     */
    protected $style;

    /**
     * BadgeGenerator constructor.
     *
     * @param string $subject
     * @param string $value
     * @param string $color
     * @param string $style
     */
    protected function __construct(
        $subject,
        $value,
        $color='fcfcfc',
        $style=self::STYLE_DEFAULT
    ){

        $this->style = strtolower($style);

        switch ($this->style){

            case self::STYLE_FLAT_SQUARE:
                $this->renderer = new SvgFlatSquareRender(); break;

            case self::STYLE_FLAT:
            case self::STYLE_DEFAULT:
                $this->renderer = new SvgFlatRender(); break;

            case self::STYLE_PLASTIC:
                $this->renderer = new SvgRender(); break;

            default:
                throw new \InvalidArgumentException("Unsupported style {$style} ... .");

        }

        // Generate badge as svg
        $poser       = new Poser([$this->renderer]);

        // TODO this hopefully throws when generation fails
        $this->badge = $poser->generate($subject, $value, $color, $this->style);

    }

    /**
     * Saves generated badge to file
     * @param string $filename
     *
     * @return bool|int|string
     */
    public function save( $filename='badge.svg' ) {

        if( empty( $filename ) )
            throw new \InvalidArgumentException( "Please enter a file name ... ." );

        $ext = strtolower(
            substr($filename, strrpos($filename, '.')+1)
        );

        if( strpos($filename, '.') and 'svg' != $ext )
            throw new \InvalidArgumentException("Filename should be an svg. (e.g. badge.svg)");

        return file_put_contents($filename, $this->badge->__toString());

    }

    /**
     * Generates a badge svg image
     * @param string $subject
     * @param string $value
     * @param string $color
     * @param string $style
     *
     * @return static
     */
    public static function make( $subject, $value, $color='ffffff', $style=self::STYLE_DEFAULT ) {

        //Generate new badge
        return new static(
            $subject,
            $value,
            $color,
            $style
        );

    }

    /**
     * Retrieve badge svg as string
     * @return string
     */
    public function getSvg(){

        return $this->badge->__toString();

    }

}