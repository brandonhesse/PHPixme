<?php

namespace PHPixme;

/**
 * Class Assert
 * @package PHPixme
 * @internal
 */
final class Assert
{
    public static function isCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('callback must be a callable function');
        }
        return $callable;
    }

    public static function isPositiveOrZero($number)
    {
        if (!is_integer($number) || $number < 0) {
            throw new \InvalidArgumentException('argument must be a integer 0 or greater');
        }
        return (int)$number;
    }

    public static function isTraversable($arrayLike)
    {
        if (!is_array($arrayLike) && !($arrayLike instanceof \Traversable)) {
            throw new \InvalidArgumentException('argument must be a Traversable or array');
        }
        return $arrayLike;
    }
}