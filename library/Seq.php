<?php

namespace Phpixme;

/**
 * Class Seq
 * @package Phpixme
 */
class Seq implements NaturalTransformationInterface, \Countable
{
    private $hash = [];
    private $keyR = [];
    private $length = 0;
    // -- iterator state --
    private $pointer = 0;
    // == iterator state ==


    /**
     * Seq constructor.
     * @param \Traversable|array|\PHPixme\NaturalTransformationInterface $arrayLike
     */
    public function __construct($arrayLike)
    {
        $this->hash = static::arrayLikeToArray($arrayLike);
        $this->keyR = array_keys($this->hash);
        $this->length = count($this->hash);
    }

    protected static function arrayLikeToArray($arrayLike)
    {
        if ($arrayLike instanceof NaturalTransformationInterface) {
            return $arrayLike->toArray();
        }
        Assert::isTraversable($arrayLike);
        if (is_array($arrayLike)) {
            return $arrayLike;
        }

        $output = [];
        foreach ($arrayLike as $key => $value) {
            $output[$key] = $value;
        }
        return $output;
    }

    /**
     * Calls the constructor with the array like parameter
     * @param $arrayLike
     * @return static
     */
    public static function from($arrayLike)
    {
        return new static($arrayLike);
    }

    public static function of(...$args)
    {
        return new static($args);
    }

    public function __invoke($offeset)
    {
        return isset($this->hash[$offeset]) ? $this->hash[$offeset] : null;
    }

    public function map(callable $hof)
    {
        $output = [];
        foreach ($this->hash as $key => $value) {
            $output[$key] = $hof($value, $key, $this);
        }
        return static::from($output);
    }

    public function filter(callable $hof)
    {
        $output = [];
        foreach ($this->hash as $key => $value) {
            if ($hof($value, $key, $this)) {
                $output[$key] = $value;
            }
        }
        return static::from($output);
    }

    public function filterNot(callable $hof)
    {
        $output = [];
        foreach ($this->hash as $key => $value) {
            if (!($hof($value, $key, $this))) {
                $output[$key] = $value;
            }
        }
        return static::from($output);
    }

    public function flatMap(callable $hof)
    {
        return static::from(call_user_func_array('array_merge', map(function ($value, $key) use ($hof) {
            return static::arrayLikeToArray($hof($value, $key, $this));
        }, $this->hash)));
    }

    public function flatten()
    {
        return static::from(call_user_func_array('array_merge', map(function ($value) {
            return static::arrayLikeToArray($value);
        }, $this->hash)));
    }

    /**
     * @inheritdoc
     */
    public function fold(callable $hof, $startVal)
    {
        $output = $startVal;
        foreach ($this->hash as $key => $value) {
            $output = $hof($output, $value, $key, $this);
        }
        return $output;
    }

    public function forAll(callable $predicate)
    {
        foreach ($this->hash as $key => $value) {
            if (!($predicate($value, $key, $this))) {
                return false;
            }
        }
        return true;
    }

    public function forSome(callable $predicate)
    {
        foreach ($this->hash as $key => $value) {
            if ($predicate($value, $key, $this)) {
                return true;
            }
        }
        return false;
    }

    public function forNone(callable $predicate)
    {
        foreach ($this->hash as $key => $value) {
            if ($predicate($value, $key, $this)) {
                return false;
            }
        }
        return true;
    }

    public function reduce(callable $hof)
    {
        if ($this->length < 1) {
            throw new \OutOfRangeException('Cannot reduce a set of 0, this behavior is undefined');
        }
        // Make a copy to shift the first value
        $keyR = $this->keyR;
        $output = $this->hash[array_shift($keyR)];
        foreach ($keyR as $key) {
            $output = $hof($output, $this->hash[$key], $key, $this);
        }
        return $output;
    }

    public function union(...$arrayLikeN)
    {
        array_unshift($arrayLikeN, $this->hash);
        return static::from(call_user_func_array('array_merge', map(function ($value) {
            return static::arrayLikeToArray($value);
        }, $arrayLikeN)));
    }


    public function find(callable $hof)
    {
        $found = null;
        foreach ($this->keyR as $key) {

            if ($hof($this->hash[$key], $key, $this)) {
                $found = $this->hash[$key];
                break;
            }
        }
        return Maybe($found);
    }

    public function walk(callable $hof)
    {
        foreach ($this->keyR as $key) {
            $hof($this->hash[$key], $key, $this);
        }
    }

    public function head()
    {
        return $this->isEmpty() ? null : $this->hash[$this->keyR[0]];
    }

    public function tail()
    {
        if ($this->length > 1) {
            $tail = $this->hash;
            array_shift($tail);
            return $this::from($tail);
        }
        return $this::from([]);
    }

    public function indexOf($thing)
    {
        $key = array_search($thing, $this->hash, true);
        return $key === false ? -1 : $key;
    }

    public function partition($hof)
    {
        Assert::isCallable($hof);
        $true = [];
        $false = [];
        foreach ($this->hash as $key => $value) {
            if ($hof($value, $key, $this)) {
                $true[$key] = $value;
            } else {
                $false[$key] = $value;
            }
        }
        return static::of(static::from($false), static::from($true));
    }

    public function group($hof)
    {
        Assert::isCallable($hof);
        return static::from(
            map(
                function ($value) {
                    return static::from($value);
                }
                , fold(
                    function ($output, $value, $key) use ($hof) {
                        $groupKey = (string)$hof($value, $key, $this);
                        if (!isset($output[$groupKey])) {
                            $output[$groupKey] = [];
                        }
                        $output[$groupKey][$key] = $value;
                        return $output;
                    }
                    , []
                    , $this->hash
                )
            )
        );
    }

    public function drop($number = 0)
    {
        return $this::from(array_slice($this->hash, $number, null, true));
    }

    public function dropRight($number = 0)
    {
        return $this::from(array_slice($this->hash, 0, -1 * $number, true));
    }

    public function take($number = 0)
    {
        return $this::from(array_slice($this->hash, 0, $number, true));
    }

    public function takeRight($number = 0)
    {
        return $this::from(array_slice($this->hash, -1 * $number, null, true));
    }

    public function toArray()
    {
        return $this->hash;
    }

    public function values () {
        return static::from(array_values($this->hash));
    }

    public function keys() {
        return static::from($this->keyR);
    }


    public function isEmpty()
    {
        return $this->length === 0;
    }
    /**
     * Gets the number of the contained in the collection
     * @return integer
     */
    public function count () {
        return $this->length;
    }

    public function toString($glue = ',')
    {
        return implode($glue, $this->hash);
    }

    public function toJson()
    {
        return json_encode($this->hash);
    }

    public function reverse()
    {
        return $this::from(array_reverse($this->hash, true));
    }


    // -- iterator interface --
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->valid() ? $this->hash[$this->keyR[$this->pointer]] : null;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->pointer += 1;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->valid() ? $this->keyR[$this->pointer] : null;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->length > $this->pointer;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        if ($this->length > 0) {
            $this->pointer = 0;
        }
    }
    // == iterator interface ==
}
