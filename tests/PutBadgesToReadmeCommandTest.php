<?php
/**
 * Candybar - Test for AddBadgesToReadmeCommand class
 * @author adrian7
 * @version 1.0
 */

class PutBadgesToReadmeCommandTest extends CliCommandTest {

    /**
     * List of badges files
     * @var array
     */
    public static $badges = [
        'one.svg',
        'two.svg',
        'three.svg'
    ];

    /**
     * Folder for badges
     * @var string
     */
    public static $folder = ( __DIR__ . '/data' );

    public static function setUpBeforeClass() {

        // Generate some badges

        $colors = [
            '248888',
            'E7475E',
            'F0D879'
        ];

        foreach ( self::$badges as $i=>$badge ) {
            \DevLib\Candybar\Graphics\BadgeGenerator::make(
                'test',
                'rest',
                $colors[$i]
            )->save(self::$folder . DIRECTORY_SEPARATOR . $badge);
        }

    }

    public static function tearDownAfterClass() {

        // Cleanup
        foreach ( self::$badges as $badge )
            @unlink(self::$folder . DIRECTORY_SEPARATOR . $badge );

    }

    /**
     * @throws Exception
     */
    public function testCommand(){

        $template = ( __DIR__ . '/data/template.md' );
        $output   = ( __DIR__ . '/data/output.md' );
        $backup   = ( __DIR__ . '/data/output.md.bk' );

        $this->silent('readme:add-badges', [
            self::$folder,
            "--template={$template}",
            "--output={$output}",
            "--backup",
            "--img"
        ]);

        // Did we made a backup?
        $this->assertFileExists($backup);

        // Did the badges were replaced?
        $generated = @file_get_contents($output);

        foreach (self::$badges as $badge)
            $this->assertContains($badge, $generated);

        // Did we replaced the html?
        $this->assertContains('<img', $generated);
        $this->assertContains('src', $generated);

        // Cleanup
        @unlink($backup);
        file_put_contents($output, '# Test output' . PHP_EOL);

    }

    public function testWarnsWhenNoBadgesFoundInTemplate(){
        // TODO...
    }

    /**
     * @throws Exception
     */
    public function testFailsWhenFileNotFound(){

        $this->expectException(\InvalidArgumentException::class);

        $this->silent('readme:add-badges', [
            self::$folder,
            "--template=missing.template.md"
        ]);

    }

    /**
     * @throws Exception
     */
    public function testFailsWhenBadgesFolderNotFound(){

        $this->expectException(\InvalidArgumentException::class);

        $template = ( __DIR__ . '/data/template.md' );

        $this->silent('readme:add-badges', [
            "/is/unreadable/folder",
            "--template={$template}"
        ]);
    }

}