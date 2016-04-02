<?php

use Phpixme as p;

if (!function_exists('curry')) {
    /**
     * Take a callable and produce a curried \Closure
     * @param int $arity
     * @param callable = $hof
     * @return \Closure
     */
    function curry($arity, $hof = null)
    {
        return call_user_func_array(p\Phpixme::curry(), func_get_args());
    }
}

if (!function_exists('nAry')) {
    /**
     * Wrap a function in an argument that will eat all but n arguments
     * @param int $arity
     * @param callable = $hof
     * @return \Closure
     */
    function nAry($arity, $hof = null)
    {
        return call_user_func_array(p\Phpixme::nAry(), func_get_args());
    }
}

if (!function_exists('unary')) {
    /**
     * wrap a callable in a function that will eat but one argument
     * @param callable $hof
     * @return \Closure
     */
    function unary($hof)
    {
        p\Assert::isCallable($hof);
        return function ($arg) use (&$hof) {
            return $hof($arg);
        };
    }
}

if (!function_exists('binary')) {
    /**
     * Wrap a callable in a function that will eat all but two arguments
     * @param callable $hof
     * @return \Closure
     */
    function binary($hof)
    {
        p\Assert::isCallable($hof);
        return p\Phpixme::__curry(2, function ($x, $y) use (&$hof) {
            return $hof($x, $y);
        });
    }
}

if (!function_exists('ternary')) {
    /**
     * Wrap a callable function in one that will eat all but three arguments
     * @param callable $hof
     * @return \Closure
     */
    function ternary($hof)
    {
        p\Assert::isCallable($hof);
        return p\Phpixme::__curry(3, function ($x, $y, $z) use ($hof) {
            return $hof($x, $y, $z);
        });
    }
}

if (!function_exists('nullary')) {
    /**
     * Wrap a function in one that will eat all arguments
     * @param $hof
     * @return \Closure
     */
    function nullary($hof)
    {
        p\Assert::isCallable($hof);
        return function () use ($hof) {
            return $hof();
        };
    }
}

if (!function_exists('flip')) {
    /**
     * Takes a callable, then flips the two next arguments before calling the function
     * @param callable
     * @return \Closure f(a, b, ....z) -> f(b,a, ... z)
     */
    function flip($hof)
    {
        p\Assert::isCallable($hof);
        return p\Phpixme::__curry(2, function (...$args) use ($hof) {
            $temp = $args[0];
            $args[0] = $args[1];
            $args[1] = $temp;
            return call_user_func_array($hof, $args);
        });
    }
};

if (!function_exists('combine')) {
    /**
     * Takes two functions and has the first consume the output of the second,
     * combining them to a single function
     * @sig x y z -> x(y(z))
     * @param callable $hofSecond
     * @param callable = $hofFirst
     * @return \Closure
     */
    function combine($hofSecond, $hofFirst = null)
    {
        return call_user_func_array(p\Phpixme::combine(), func_get_args());
    }
}

if (!function_exists('K')) {
    /**
     * @param mixed $first
     * @return \Closure
     * @sig first -> ignored -> first
     */
    function K($first)
    {
        return function ($ignored = null) use (&$first) {
            return $first;
        };
    }
}

if (!function_exists('KI')) {
    /**
     * @param $ignored = This parameter will be ignored
     * @return \Closure
     * @sig ignored -> second -> second
     */
    function KI($ignored = null)
    {
        return unary('I');
    }
}

if (!function_exists('I')) {
    /**
     * @param mixed $x
     * @return mixed $x
     * @sig x -> x
     */
    function I($x)
    {
        return $x;
    }
}


if (!function_exists('S')) {
    /**
     * @param callable $x
     * @param callable = $y
     * @param mixed = $z
     * @return \Closure|mixed
     * @sig x, y, z -> x(z)(y(z)
     */
    function S($x, $y = null, $z = null)
    {
        return call_user_func_array(p\Phpixme::S(), func_get_args());
    }/**
 * @param callable $hof
 * @param \Traversable= $traversable
 * @return \Closure|mixed
 */
function reduce($hof, $traversable = null)
{
    return call_user_func_array(p\Phpixme::reduce(), func_get_args());
}
}

if (!function_exists('fold')) {
    /**
     * @param callable $hof
     * @param mixed = $startVal
     * @param \Traversable= $traversable
     * @return \Closure|mixed
     */
    function fold($hof, $startVal = null, $traversable = null)
    {
        return call_user_func_array(p\Phpixme::fold(), func_get_args());
    }
}


if (!function_exists('reduce')) {
    /**
     * @param callable $hof
     * @param \Traversable= $traversable
     * @return \Closure|mixed
     */
    function reduce($hof, $traversable = null)
    {
        return call_user_func_array(p\Phpixme::reduce(), func_get_args());
    }
}

if (!function_exists('map')) {
    /**
     * @param callable $hof
     * @param array|\Traversable|\PHPixme\NaturalTransformationInterface $traversable
     * @return \Closure|mixed
     */
    function map(callable $hof, $traversable = null)
    {
        return call_user_func_array(p\Phpixme::map(), func_get_args());
    }
}

if (!function_exists('callWith')) {
    /**
     * Produce a function that calls a function within a array or object
     * @param string $accessor
     * @param object|array $container =
     * @return \Closure ($container) -> ((args) -> $container{[$accessor]}(...args))
     */
    function callWith($accessor, $container = null)
    {
        return call_user_func_array(p\Phpixme::callWith(), func_get_args());
    }
}

if (!function_exists('pluckObjectWith')) {
    /**
     * Creates a function to access the property of an object
     * @param string $accessor
     * @return \Closure ($object) -> object->accessor
     */
    function pluckObjectWith($accessor)
    {
        return function ($container) use ($accessor) {
            return $container->{$accessor};
        };
    }
}

if (!function_exists('pluckArrayWith')) {
    /**
     * Creates a function to access the property of an object
     * @param string $accessor
     * @return \Closure ($object) -> object->accessor
     */
    function pluckArrayWith($accessor)
    {
        return function ($container) use ($accessor) {
            return $container[$accessor];
        };
    }
}
