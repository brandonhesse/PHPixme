<?php

/**
 * @param callable $hof
 * @return Phpixme\Success|Phpixme\Failure
 */
function Attempt(callable $hof)
{
    try {
        return Success($hof());
    } catch (\Exception $e) {
        return Failure($e);
    }
}

/**
 * @param $value
 * @return Phpixme\Success
 */
function Success($value)
{
    return new Phpixme\Success($value);
}

/**
 * @param $exception - The failure value
 * @return Phpixme\Failure
 */
function Failure ($exception) {
    return new Phpixme\Failure($exception);
}

/**
 * Takes a value and wraps it in a Maybe family object
 * @param $x - the maybe existing value
 * @return Phpixme\None|Phpixme\Some
 */
function Maybe($x = null)
{
    return (
        !isset($x) || is_null($x) ||
        (is_array($x) && count($x) === 0)
    ) ?
        None()
        : Some($x);
}

/**
 * Get the None Singleton
 * @return Phpixme\None
 */
function None()
{
    return Phpixme\None::getInstance();
}

/**
 * @param $x - a non- null value
 * @return Phpixme\Some
 */
function Some($x)
{
    return new Phpixme\Some($x);
}

/**
 * @param $arrayLike
 * @return Phpixme\Seq
 */
function Seq($arrayLike)
{
    return new Phpixme\Seq($arrayLike);
}
