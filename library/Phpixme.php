<?php

namespace Phpixme;

/**
 * @internal
 */
final class Phpixme
{
    private static $OPERATIONS = [];

    public static function curry()
    {
        if (!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, __CLASS__ . '::__curry'));
        }

        return self::get(__FUNCTION__);
    }

    public static function nAry()
    {
        if (!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, function ($number = 0, $hof = null) {
                Assert::isPositiveOrZero($number);
                Assert::isCallable($hof);
                return function () use (&$number, &$hof) {
                    $args = func_get_args();
                    return call_user_func_array($hof, array_slice($args, 0, $number));
                };
            }));
        }

        return self::get(__FUNCTION__);
    }

    public static function combine()
    {
        if (!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, function ($x, $y) {
                Assert::isCallable($x);
                Assert::isCallable($y);
                return function ($z) use ($x, $y) {
                    return call_user_func($x, call_user_func($y, $z));
                };
            }));
        }
        
        return self::get(__FUNCTION__);
    }

    public static function S()
    {
        if(!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(3, function ($x, $y, $z) {
                Assert::isCallable($x);
                Assert::isCallable($y);
                $x_z = call_user_func($x, $z);
                Assert::isCallable($x_z);
                return call_user_func($x_z, call_user_func($y, $z));
            }));
        }

        return self::get(__FUNCTION__);
    }

    public static function fold() {
        if(!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(3, function ($hof, $startVal, $arrayLike) {
                Assert::isCallable($hof);
                if ($arrayLike instanceof NaturalTransformationInterface) {
                    return $arrayLike->fold($hof, $startVal);
                }
                Assert::isTraversable($arrayLike);
                $output = $startVal;
                foreach ($arrayLike as $key => $value) {
                    $output = call_user_func($hof, $output, $value, $key, $arrayLike);
                }
                return $output;
            }));
        }

        return self::get(__FUNCTION__);
    }

    public static function reduce() {
        if(!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, function ($hof, $arrayLike) {
                Assert::isCallable($hof);
                if ($arrayLike instanceof NaturalTransformationInterface) {
                    return $arrayLike->reduce($hof);
                }
                Assert::isTraversable($arrayLike);
                $iter = is_array($arrayLike) ? new \ArrayIterator($arrayLike) : $arrayLike;
                $iter->rewind();
                if (!$iter->valid()) {
                    throw new \InvalidArgumentException('Cannot reduce on collection of less than one. Behaviour is undefined');
                }
                $output = $iter->current();
                $iter->next();
                while ($iter->valid()) {
                    $output = call_user_func($hof, $output, $iter->current(), $iter->key(), $arrayLike);
                    $iter->next();
                }
                return $output;
            }));
        }

        return self::get(__FUNCTION__);
    }
    
    public static function map() {
        if(!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, function (callable $hof, $traversable) {

                // Reflect on natural transformations
                if ($traversable instanceof NaturalTransformationInterface) {
                    return $traversable->map($hof);
                }
                Assert::isTraversable($traversable);
                $output = [];
                foreach ($traversable as $key => $value) {
                    $output[$key] = call_user_func($hof, $value, $key, $traversable);
                }
                return $output;
            }));
        }

        return self::get(__FUNCTION__);
    }

    public static function callWith()
    {
        if(!self::has(__FUNCTION__)) {
            self::set(__FUNCTION__, self::__curry(2, function ($accessor, $container) {
                $callable = is_array($container) ?
                    (isset($container[$accessor]) ? $container[$accessor] : null)
                    : [$container, $accessor];
                Assert::isCallable($callable);
                return function () use (&$callable) {
                    return call_user_func_array($callable, func_get_args());
                };
            }));
        }

        return self::get(__FUNCTION__);
    }
    
    /** For curry */
    private static function curryGiven($prevArgs, $arity, $callable)
    {
        return function (...$newArgs) use ($arity, $callable, $prevArgs) {
            $args = array_merge($prevArgs, $newArgs);
            if (count($args) >= $arity) {
                return call_user_func_array($callable, $args);
            }
            return static::curryGiven($args, $arity, $callable);
        };
    }
    
    /** Helpers for the internal static array */
    private static function has($name)
    {
        return isset(static::$OPERATIONS[$name]);
    }

    private static function get($name)
    {
        return static::$OPERATIONS[$name];
    }

    private static function set($name, $callable)
    {
        static::$OPERATIONS[$name] = $callable;
    }

    /**
     * Uncurried curry function for internal use
     * @param int $arity
     * @param callable $callable
     * @return \Closure
     */
    public static function __curry($arity = 0, callable $callable)
    {
        Assert::isPositiveOrZero($arity);
        Assert::isCallable($callable);

        return static::curryGiven([], $arity, $callable);
    }
}