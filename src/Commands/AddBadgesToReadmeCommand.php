<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

use DevLib\Candybar\Exceptions\UnreadableFileException;
use DevLib\Candybar\Util;

class AddBadgesToReadmeCommand extends Command{

    protected $description =
        "Links svg badges from folder to placeholders `<badge>` in your readme file. 
        The command uses a template file (by default README.template.md) and builds the 
        README.md from it. Use the --backup option to make a backup of the README.md 
        before replacing it.";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        'folder' => [
            'default'     => 'tests/logs/badges',
            'description' => 'The folder to look for svg files.'
        ]

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        'template' => [
            'default'     => 'README.template.md',
            'description' =>
                'The readme file with <tags> to add use as template. Default is README.template.md'
        ],

        'output' => [
            'default'     => 'README.md',
            'description' => 'The readme file to build. Default is README.md'
        ],

        'backup' => [
            'default'     => FALSE,
            'description' =>
                'Save the original readme.md as readme.md.bk before building a new one'
        ]

    ];

    /**
     * @throws UnreadableFileException
     */
    public function handle() {

        $output   = $this->option('output');
        $template = $this->option('template');
        $folder   = rtrim( $this->argument('folder'), DIRECTORY_SEPARATOR );

        if( ! file_exists($template) ){

            if( $template = Util::lookupFile($template) );
            else
                //Try lowercase file name
                $template = Util::lookupFile( strtolower($template) );

        }


        if( ! $template )
            //Could not find template file
            throw new \InvalidArgumentException(
                sprintf(
                    "Could not find readme file at %s ... .",
                    $this->option('template')
                )
            );


        if( ! is_dir($folder) )
            throw new \InvalidArgumentException( "Cannot find folder {$folder} ... ." );

        $badges   = glob("$folder/*svg");
        $contents = file_get_contents($template);

        //Lookup for badges and generate contents
        foreach ( $badges as $badge ) {

            if( ! is_file($badge) )
                continue;

            $tag = str_replace(
                ['-', 'badge', '.svg'],
                '',
                basename(strtolower($badge))
            );

            $alt  = basename($badge);
            $path = str_replace(dirname($output), '.', $badge);

            //Generate replacement tag
            $link = sprintf(
                '![%s](%s)<img src="%s">',
                $alt,
                $path,
                $path
            );

            //$this->line("Found badge " . basename($badge) );
            //$this->line("Replacing <{$tag}> with svg in {$template} ... ." );

            $contents = str_replace( "<$tag>", $link, $contents );

        }

        if( $contents != file_get_contents($template) ){

            //Output the new readme

            if( is_file($output) and $this->option('backup') )
                //Save a backup of the output before overwriting
                copy($output, "$output.bk");

            if( file_put_contents($output, $contents) )
                $this->info("Built new readme file as {$output}");
            else
                throw new UnreadableFileException("Could not save file {$output} ... .");
        }
        else
            //No tags replaced
            $this->warn(
                "No badges replaced in {$template}. Please add some compatible tags... ."
            );

    }

}