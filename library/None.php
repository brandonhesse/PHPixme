<?php

namespace Phpixme;

/**
 * Class None
 * @package PHPixme
 * This class is a singleton! Please us it's Function companion for eas of use!
 */
class None extends Maybe
{
    protected static $instance = null;

    public function contains($x)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function exists(callable $hof)
    {
        return false;
    }


    /**
     * @inheritdoc
     */
    public function get()
    {
        throw new \Exception('Cannot get on None!');
    }

    /**
     * @inheritdoc
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    // -- Natural Transformation Static --
    /**
     * @inheritdoc
     */
    public static function of(...$args)
    {
        return static::getInstance();
    }

    /**
     * @inheritdoc
     */
    public static function from($args)
    {
        return static::getInstance();
    }
    // == Natural transformation Static ==

    // -- Magic Methods --
    protected function __clone()
    {
        // This space intentionally left blank
    }

    protected function __wakeup()
    {
        // This space intentionally left blank
    }

    protected function __construct()
    {
        // This space intentionally left blank
    }

    // == Magic Methods ==

    // -- Natural Transformation interface methods --
    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function find(callable $hof)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function flatten()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function flatMap(callable $hof)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $hof)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterNot(callable $hof)
    {
        return $this;
    }


    /**
     * @inheritdoc
     */
    public function fold(callable $hof, $startVal)
    {
        return $startVal;
    }

    /**
     * @inheritdoc
     */
    public function forAll(callable $predicate)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function forNone(callable $predicate)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function forSome(callable $predicate)
    {
        return false;
    }


    /**
     * @inheritdoc
     */
    public function map(callable $hof)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $hof)
    {
        throw new \InvalidArgumentException('Cannot reduce on None. Behaviour is undefined');
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function walk(callable $hof)
    {
        return $this;
    }

    // == Natural Transformation interface methods ==

    // -- Iterator interface methods--
    public function key()
    {
        return null;
    }

    /**
     * None is always at it's end
     * @return false
     */
    public function valid()
    {
        return false;
    }

    public function next()
    {
        // This space is intentionally left blank
    }

    public function rewind()
    {
        // This space is intentionally left blank
    }

    public function current()
    {
        return null;
    }
    // == Iterator interface methods==

    // -- Countable Interface --
    public function count()
    {
        return 0;
    }
    // == Countable Interface ==
}
