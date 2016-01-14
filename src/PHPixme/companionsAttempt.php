<?php
namespace PHPixme;
const Attempt = __NAMESPACE__.'\Attempt';

/**
 * @param callable $hof
 * @return Success|Failure
 */
function Attempt(callable $hof)
{
    try {
        return Success($hof());
    } catch (\Exception $e) {
        return Failure($e);
    }
}

const Success = __NAMESPACE__.'\Success';
/**
 * @param $value
 * @return Success
 */
function Success($value)
{
    return new Success($value);
}


const Failure = __NAMESPACE__.'\Failure';
/**
 * @param $exception - The failure value
 * @return Failure
 */
function Failure ($exception) {
    return new Failure($exception);
}