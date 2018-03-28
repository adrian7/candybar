<?php
/**
 * Candybar - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Commands;

class ExampleCommand extends Command{

    protected $description = "An example command. Boilerplate code for your own commands";

    /**
     * Command arguments
     * @var array
     */
    protected $arguments = [

        //An argument with default value and description
        'one' => [
            'default'     => 'value',
            'description' => 'Just a placeholder argument'
        ],

        //An argument without description
        'two' => 'defaultValue'

    ];

    /**
     * Command long options (--option)
     * @var array
     */
    protected $options = [

        //Required option
        'account' => [
            'required'      => TRUE,
            'description'   => 'Account option is required'
        ],

        //Option with default value and description
        'key' => [
            'default'     => NULL,
            'description' => 'The option description'
        ],

        //Option without a description
        'secret' => NULL,

        //Boolean option (switch)
        'erase' => FALSE

    ];

    /**
     * Handle command
     */
    public function handle() {

        $this->line(" Hello world! I'm an example command.");
        $this->eol(
            sprintf(
                " You selected the '%s' account" . PHP_EOL,
                $this->option('account')
            )
        );

        //Display arguments
        $this->eol(" Arguments: ");

        foreach (array_keys($this->arguments) as $arg)
            if( $v = $this->argument($arg) )
                $this->eol("  - {$arg}=" . strval($v) );

        //Display options
        $this->line(" Options");

        foreach (array_keys($this->options) as $opt)
            if( $v = $this->option($opt) )
                $this->eol("  - {$opt}=" . strval($v) );

        $this->eol();

    }

}