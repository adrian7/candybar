<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar;

use DevLib\Candybar\Exceptions\IoCRepositoryException;

class Repository{

    /**
     * List of resolvers registered
     * @var array
     */
    protected static $registry = [];

    /**
     * Add a new resolver to the registry array.
     * @param  string $name: The id
     * @param  \Closure $resolve: Closure that creates instance
     * @return void
     *
     * @throws IoCRepositoryException
     */
    public static function register($name, \Closure $resolve) {

        $name = strtolower( strval($name) );

        if( isset(static::$registry[$name]) )
            throw new IoCRepositoryException(
                "A resolver for {$name} is already registered... ."
            );

        static::$registry[$name] = $resolve;

    }

    /**
     * Resolve/Create an instance
     * @param  string $name The id
     * @return mixed
     *
     * @throws IoCRepositoryException
     */
    public static function resolve($name)
    {

        if ( static::registered($name) ) {

            $name = static::$registry[$name];

            return $name();

        }

        throw new IoCRepositoryException(
            "No resolver registered for {$name} ... ."
        );
    }

    /**
     * Determine whether the id is registered
     * @param  string $name The id
     * @return bool Whether to id exists or not
     */
    public static function registered($name) {
        return array_key_exists($name, static::$registry);
    }

}