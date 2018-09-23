<?php
/**
 * Candybar - CLI tests
 * @author adrian7
 * @version 1.0
 */

class CliTest extends CliCommandTest {

    protected static $installDir;

    public static function tearDownAfterClass() {

        // Cleanup install dir
        $sys = new \Symfony\Component\Filesystem\Filesystem();
        $sys->remove(self::$installDir);

    }

    public function testShowsSuccessMessage(){

        $this->expectOutputRegex( "/success message/");

        $cli = new DevLib\Candybar\Cli();

        $cli->success("This is a success message!");

    }

    public function testShowsWarningMessage(){

        $this->expectOutputRegex( "/warning message/");

        $cli = new DevLib\Candybar\Cli();

        $cli->warn("This is a warning message!");

    }

    public function testShowsInfoMessage(){

        $this->expectOutputRegex( "/info message/");

        $cli = new DevLib\Candybar\Cli();

        $cli->info("This is an info message!");

    }

    /**
     * @throws Exception
     */
    public function testCliEntryPoint(){

        $_SERVER['argv'] = [
            0 => "candybar",
            1 => "help"
        ];

        // Can we see this string in the output?
        $this->expectOutputRegex( "/Usage: candybar/");

        // Fire command
        \DevLib\Candybar\Cli::main();

        // Without any command we should see the the help
        $_SERVER['argv'] = [ 0 => "candybar" ];

        // Can we see the string in the output?
        $this->expectOutputRegex( "/Usage: candybar/");

        // Fire command
        \DevLib\Candybar\Cli::main();

    }

    public function testInitCommand(){

        // Backup cwd
        $backupCWD = getcwd();
        $newCWD    = ( __DIR__ . '/data' );
        $installDir= ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' );

        // Change cwd
        chdir($newCWD);

        $this->silent('init', [], [
            'Welcome',
            \DevLib\Candybar\Cli::VERSION
        ]);

        // Did the files were copied?
        $this->assertFileExists(
            $installDir . DIRECTORY_SEPARATOR . 'config.php'
        );

        $this->assertFileExists(
            $installDir . DIRECTORY_SEPARATOR . 'styles/default.css'
        );

        // Set install dir
        self::$installDir = $installDir;

        // Restore cwd
        chdir($backupCWD);

    }

    /**
     * @throws \DevLib\Candybar\Exceptions\InvalidConfigurationException
     * @throws \DevLib\Candybar\Exceptions\UnreadableFileException
     */
    public function testHelpCommand(){

        // Reset command runner
        $this->setUp(TRUE);

        $this->silent('help', [], [
            'help',
            'Usage',
            \DevLib\Candybar\Cli::VERSION
        ]);

    }

    public function testListCommand(){

        // Test list command with default commands
        $this->silent('list', [], [
            'coverage:style',
            'coverage:badge',
            'example:command'
        ]);

    }

    public function testCommandShowsHelp(){

        // Test if help is displayed for example:command
        $this->silent('help', ['example:command'], [
            'An example command. Boilerplate code for your own commands',
            'Usage:',
            'example:command'
        ]);

    }

    /**
     * @throws Exception
     */
    public function testVersionCommand(){

        $_SERVER['argv'] = [
            0 => "candybar",
            1 => "version"
        ];

        // Can we see this string in the output?
        $this->expectOutputRegex(
            join('', [ "/", "Candybar v", \DevLib\Candybar\Cli::VERSION, "/" ])
        );

        // Fire command
        \DevLib\Candybar\Cli::main();

    }

    public function testFailsToInstallWhenPathNotWritable(){

        $installDir = '/tmp/theresNothingToDoHere';

        // Expecting exception
        $this->expectException( \InvalidArgumentException::class );

        // Create tmp dir
        mkdir($installDir);

        // Change current directory
        chdir( $installDir );

        // Remove dir
        rmdir($installDir);

        // Execute init command
        $this->silent('init');

    }

    /**
     * @depends testInitCommand
     */
    public function testThrowsIncompleteInstallExceptionIfConfigFileIsMissing(){

        // Expecting exception
        $this->expectException(
            \DevLib\Candybar\Exceptions\IncompleteInstallationException::class
        );

        $newCWD    = ( __DIR__ . '/data' );
        $installDir= ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' );

        chdir( $newCWD );

        // Remove config file
        @unlink( $installDir . DIRECTORY_SEPARATOR . 'config.php' );

        $this->silent('init');

    }

    /**
     * @depends testInitCommand
     */
    public function testThrowsIncompleteInstallExceptionIfStyledDirIsMissing(){

        // Expecting exception
        $this->expectException(
            \DevLib\Candybar\Exceptions\IncompleteInstallationException::class
        );

        $newCWD     = ( __DIR__ . '/data' );
        $installDir = ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' );

        chdir( $newCWD );

        // Rename styles folder
        @rename(
            $installDir . DIRECTORY_SEPARATOR . 'styles',
            $installDir . DIRECTORY_SEPARATOR . 'renamed'
        );

        $this->silent('init');

    }

    public function testThrowsInvalidConfigurationException(){

        //Expecting exception
        $this->expectException(
            \DevLib\Candybar\Exceptions\InvalidConfigurationException::class
        );

        $installDir = ( __DIR__ . '/data/invalid-config' );

        chdir( $installDir );

        $this->silent('list');

    }

    public function testThrowsExceptionForUnknownCommand(){

        // Expecting an exception
        $this->expectException(
            \DevLib\Candybar\Exceptions\UnknownCommandException::class
        );

        $this->silent('unknown');

    }

    /**
     * @throws Exception
     */
    public function testExitsWithErrorWhenCommandIsNotFound(){

        $_SERVER['argv'] = [
            0 => "candybar",
            1 => "unknown:command"
        ];

        // It should throw an exception
        $this->expectException(\DevLib\Candybar\Exceptions\UnknownCommandException::class);

        // Suppress output
        $this->setOutputCallback(function() {});

        // Fire command
        \DevLib\Candybar\Cli::main();

    }

    public function testExitsWithErrorWhenClassIsNotFound(){

        $newCWD    = ( __DIR__ . '/data/invalid-commands' );
        $installDir= ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' );

        chdir( $newCWD );

        // It should throw an exception
        $this->expectException(\ReflectionException::class);

        // Fire invalid command
        $this->silent('invalid:command');

    }

    public function testExitsWithErrorWhenClassNotImplementsCommandInterface(){

        $newCWD    = ( __DIR__ . '/data/invalid-commands' );
        $installDir= ( $newCWD . DIRECTORY_SEPARATOR . '/candybar' );

        chdir( $newCWD );

        // It should throw an exception
        $this->expectException(\ReflectionException::class);

        // Fire invalid command
        $this->silent('implements:wrong');

    }

    public function testExistsWithErrorWhenStdoutNotSupported(){

        $this->expectException( InvalidArgumentException::class );

        new DevLib\Candybar\Cli( NULL );

    }

    public function testShowsHelpWhenArrayOfOptionsHasMinusHelp(){

        $this->expectOutputRegex( "/Usage:/");

        $cli = new DevLib\Candybar\Cli();

        $cli->parseOptions([['--help']]);

    }

}