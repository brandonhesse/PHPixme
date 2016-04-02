<?php

namespace PHPixme;

interface NaturalTransformationInterface extends \Iterator
{
    // Static constructors:
    /**
     * Transfer the data from one container to another
     * @param \Traversable|array $traversable
     * @return static - A new instance of the class
     */
    public static function from($traversable);

    /**
     * A static function that accepts a list of items that will be contained by the class
     * Note: a implementing class will discard any excess if there is a container limit
     * @param mixed[] ...$items - A list of items, defining like Array
     * @return static - A new instance of the class
     */
    public static function of(...$items);

    /**
     * Fold across this class
     * @param callable $hof ($prevVal, $value, $key, $container): mixed
     * @param mixed $startVal
     * @return mixed - whatever the last cycle of $hof returns
     */
    public function fold(callable $hof, $startVal);

    /**
     * Reduce across this class
     * @param callable $hof ($prevVal, $value, $key, $container):mixed
     * @return mixed - whatever $hof returned last, or the only item contained
     * @throws \OutOfRangeException - Throws range exceptions when the container's value contains less than 1
     */
    public function reduce(callable $hof);

    /**
     * Map across the container
     * @param callable $hof ($value, $key, $container): mixed
     * @return static
     */
    public function map(callable $hof);

    /**
     * @param callable $hof ($value, $key, $container):static
     * @return static
     * @throws \Exception - if the data type returned by callback wasn't its kind
     */
    public function flatMap(callable $hof);

    /**
     * @return self
     * @throws \Exception if the data-set could not be flattened
     */
    public function flatten();

    /**
     * Filters the contents if the application of $hof returns false
     * @param callable $hof ($value, $key, $container): boolean
     * @return static
     */
    public function filter(callable $hof);

    /**
     * Filters the contents if the application of $hof returns true
     * @param callable $hof ($value, $key, $container): boolean
     * @return static
     */
    public function filterNot(callable $hof);

    /**
     * Checks to see if the $predicate applies true to all within a container
     * @param callable $predicate ($value, $key, $container): boolean
     * @return boolean
     */
    public function forAll(callable $predicate);

    /**
     * Checks to see if the $predicate applies true to none within a container
     * @param callable $predicate ($value, $key, $container): boolean
     * @return boolean
     */
    public function forNone(callable $predicate);
    /**
     * Checks to see if the $predicate applies true to at least one within a container
     * @param callable $predicate ($value, key, $container): boolean
     * @return boolean
     */
    public function forSome(callable $predicate);


    /**
     * Preform $hof over the container
     * @param callable $hof ($value, $key, $container) : null
     * @return null
     */
    public function walk(callable $hof);

    /**
     * Combine the data sets with the container
     * @param \Traversable[]|array[]|\PHPixme\NaturalTransformationInterface[] ...$traversableR
     * @return static
     */
    // Fixme: doesn't apply to single item collections
//    public function union(...$traversableR);

    /**
     * Converts the container to an array, in any structure that is appropriate within that array
     * @return array
     */
    public function toArray();

    /**
     * Is the container empty?
     * @return bool
     */
    public function isEmpty();

    /**
     * Search the container
     * @param callable $hof ($value, $key, $container):boolean
     * @return \PHPixme\Some|\PHPixme\None
     */
    public function find(callable $hof);
}
