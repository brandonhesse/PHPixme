<?php

namespace Phpixme;

/**
 * Class Maybe
 * @package Phpixme
 */
abstract class Maybe implements NaturalTransformationInterface, \Countable
{
    // -- Natural Transformation Interface Statics --
    static function of(...$args)
    {
        return Maybe($args[0]);
    }

    static function from($arg)
    {
        return Maybe($arg);
    }

    // == Natural Transformation Interface Statics ==

    /**
     * @param mixed $x - The value being checked inside
     * @return boolean - wether or not this contains it
     */
    abstract public function contains($x);

    /**
     * @param callable $hof ($value): boolean
     * @return mixed
     */
    abstract public function exists(callable $hof);


    /**
     * Gets whatever is contained
     * @return mixed - The value contained in subclass \Some
     * @throws \Exception - if of subclass None
     */
    abstract public function get();


    /**
     * If the Maybe is none, return default, else return contents
     * @param mixed $default
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->isEmpty() ? $default : $this->get();
    }

    /**
     * Does the Maybe contain a value?
     * @return bool
     */
    final public function isDefined()
    {
        return !$this->isEmpty();
    }

    public function orNull()
    {
        return $this->getOrElse(null);
    }

    /**
     * @param callable $hof () : Maybe - The default value
     * @return Maybe
     * @throws \Exception
     */
    public function orElse(callable $hof)
    {
        if ($this->isEmpty()) {
            return __assertMaybeType($hof());
        }
        return $this;
    }

    /**
     * Transforms a Maybe to a Seq
     * @return Seq
     */
    public function toSeq()
    {
        return Seq($this->toArray());
    }

    // -- NaturalTransformationInterface --
    /**
     * This form of reduce simply will return get. This will throw an error on None,
     * Since this undefined behavior to reduce on a empty collection
     * @param callable $hof
     * @return mixed
     */
    public function reduce(callable $hof)
    {
        return $this->get();
    }

    // == NaturalTransformationInterface ==

}

function __assertMaybeType($unknown)
{
    if (!($unknown instanceof Maybe)) {
        throw new \Exception('return value must be an instance of Maybe!');
    }
    return $unknown;
}
