<?php

/**
 * IrfanTOOR\Engine\DI
 * php version 7.4
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021-present Irfan TOOR
 */

namespace IrfanTOOR\Engine;

use Exception;
use Throwable;

class DI
{
    protected $ns_classes = [];
    protected $classes    = [];

    /**
     * DI constructor
     *
     * @param array $init -- [ $namespace => [ $class, ... ], ... ]
     */
    function __construct(array $init = [])
    {
        foreach ( $init as $namespace => $classes )
            $this->addNamespaceClasses( $namespace, $classes );
    }

    /**
     * Adds the namespace and the respected classes
     *
     * @param string $namespace
     * @param array  $classes
     */
    function addNamespaceClasses( string $namespace, array $classes )
    {
        $this->ns_classes[ $namespace ] = $classes;
    }

    /**
     * Adds the namespace to the classes if in the initialized list
     *
     * @param string $class
     *
     * @return string
     */
    function normalizeClass( string $class ): string
    {
        # todo -- do something to adjust for ServerRequest, Response ... etc.
        if (strpos( $class, '\\') === false ) {
            foreach ( $this->ns_classes as $namespace => $classes ) {
                if ( in_array( $class, $classes ) ) {
                    $class = $namespace . $class;
                    break;
                }
            }
        }

        return $class;
    }

    /**
     * Returns an already created class or create and return
     */
    function load( string $class )
    {
        $class = $this->normalizeClass( $class );
        $instance = self::$classes[ $class ] ?? null;
        return $instance ?? $this->create( $class );
    }

    /**
     * Creates a class, with the provided arguments
     */
    function create( string $class, ?array $args = null )
    {
        $instance = $this->createClass( $class, $args );

        if (! $instance)
            $instance = $this->createFromGlobals( $class, $args );

        if (! $instance )
            $instance = $this->__create( $class, $args );

        return $instance;
    }

    /**
     * Creates a class, with the provided arguments
     */
    protected function __create( string $class, ?array $args = null )
    {
        $class   = $this->normalizeClass( $class );
        $args    = $args ?? [];
        $list    = "";
        $sep     = "";

        foreach ( $args as $k => $arg )
        {
            if ( is_int( $k ) ) {
                $list .= $sep . '$args["' . $k . '"]';
                $sep = ', ';
            } else {
                $list = '$args';
                break;
            }
        }

        $instance = null;

        try {
            $exp = '$instance = new ' . $class . '(' . $list . ');';
            eval( $exp );
        } catch ( Throwable $th ) {
        }

        if ( $instance ) {
            # save this instance if no instance is already present
            if ( ! ( $this->classes[ $class ] ?? null ) )
                $this->classes[ $class ] = $instance;
        }

        return $instance;
    }

    /**
     * Create a class and return using the classFactory and the method createClassname
     *
     * @param string $class
     * @param array  $args
     *
     * @return null|object
     */
    function createClass( string $class, ?array $args = null )
    {
        $fclass = $this->normalizeClass( $class . "Factory" );
        $factory = $this->__create( $fclass );

        if ( ! $factory ) {
            $fclass = str_replace( $class, 'Factory\\' . $class, $fclass );
            $factory = $this->__create( $fclass );
        }

        $args = $args ?? [];

        if ( method_exists( $factory, 'create' . $class ) ) {
            return call_user_func_array(
                [ $factory, 'create' . $class ],
                $args
            );
        }

        return null;
    }

    /**
     * Create a class using its factory class
     *
     * @param string $class
     * @param array  $args
     * @return null|object
     */
    function createFromEnvironment( string $class, ?array $args = null )
    {
        $fclass = $this->normalizeClass( $class . "Factory" );
        $factory = $this->__create( $fclass );

        if ( ! $factory ) {
            $fclass = str_replace( $class, 'Factory\\' . $class, $fclass );
            $factory = $this->__create( $fclass );
        }

        $args = $args ?? [];

        if ( method_exists( $factory, 'createFromEnvironment' ) ) {
            return call_user_func_array(
                [ $factory, 'createFromEnvironment' ],
                $args
            );
        } elseif( method_exists( $factory, 'createFromGlobals' ) ) {
            return call_user_func_array(
                [ $factory, 'createFromGlobals' ],
                $args
            );
        }

        return null;
    }

    /**
     * Alias of createFromEnvironment
     *
     * @param string $class
     * @param array  $args
     *
     * return null|object
     */
    function createFromGlobals( string $class, ?array $args = null )
    {
        return $this->createFromEnvironment( $class, $args );
    }
}