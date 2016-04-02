<?php

namespace Phpixme;

/**
 * Class Attempt
 * @package PHPixme
 * @description
 * Attempt is equivalent to a Try ... Catch block in a object oriented form,
 * providing a consistency to the flow of data through functions which are
 * not applied on errors unless transformed. It also provides transformations
 * to options, and share some of it's methods, such as get, and getOrElse.
 * Note: Some of these methods will throw Exceptions if their contract
 * with a callback function is not met. They will not catch these kinds of errors.
 */
abstract class Attempt
{

    /**
     * @return boolean
     */
    abstract public function isFailure();

    /**
     * @return boolean
     */
    abstract public function isSuccess();

    /**
     * @return mixed - Get the value contained on success
     * @throws mixed - throws the value on failure
     */
    abstract public function get();

    /**
     * @param $default
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->isSuccess() ? $this->get() : $default;
    }

    /**
     * @param callable $hof (): Attempt
     * @return Attempt
     * @throws \Exception on function not returning a Attempt subtype
     */
    public function orElse(callable $hof)
    {
        if ($this->isSuccess()) {
            return $this;
        }
        try {
            $result = $hof();
        } catch (\Exception $e) {
            return Failure($e);
        }
        return __assertAttemptType($result);
    }

    /**
     * Tests a success with $hof return value, transforming a success on test failure
     * @param callable $hof ($value, $key, $container) -> boolean
     * @return Attempt
     */
    abstract public function filter(callable $hof);

    /**
     * applies a high order function across a nested success value, $hof flattening the results.
     * @param callable $hof ($value, $key, Success $container): Attempt
     * @return Attempt - Returns Success($hof($value)) or the same failure
     * @throws \Exception when the return value is not an Attempt
     */
    abstract public function flatMap(callable $hof);

    /**
     * Attempt to Flatten a Attempt(Attempt(value)) to Attempt(value)
     * @return Attempt - A de-nested Attempt
     * @throws \Exception when the return value is not an Attempt
     */
    abstract public function flatten();

    /**
     * Changes a Failure to a success.
     * If called on a Success, it will return a failure with an unsupported action exception
     * @return Attempt
     */
    abstract public function failed();

    /**
     * Map $hof across the success value
     * @param callable $hof ($value, $key, Success $container): static
     * @return Attempt - Success($hof($value)) or the same Failure as before
     */
    abstract public function map(callable $hof);

    /**
     * Tries to recover the error value as a success using $rescueException.
     * Has no affect on Success
     * @param callable $rescueException ($value, $container): mixed ! \Exception
     * @return Attempt - If return, type Success, If Thrown, type Failure
     */
    abstract public function recover(callable $rescueException);

    /**
     * Recovers using a $hof which returns either a Success or Failure.
     * @param callable $hof ($value, $container) : Attempt
     * @return Attempt
     * @throws \Exception When $rescueException does not return an Attempt type
     */
    abstract public function recoverWith(callable $hof);


    /**
     * Converts the value of success or failure to a keyed array with the instance type pointing to value
     * @return array
     */
    public function toArray() {
        try {
            return ['success'=> $this->get()];
        } catch (\Exception $e) {
            return ['failure' => $e];
        }
    }

    /**
     * Converts Success to a Some, Converts Failure to None
     * @return Maybe
     */
    public function toMaybe()
    {
        return $this->isSuccess() ? Some($this->get()) : None();
    }

    /**
     * Transforms a success or failure given the criteria presented,
     *      able to change the internal value or container from one Attempt to another
     * @param callable $success ($value, Success $container): Attempt
     * @param callable $failure ($value, Failure $container): Attempt
     * @return Attempt
     * @throws \Exception If either function returns a non Attempt
     */
    abstract public function transform(callable $success, callable $failure);

    /**
     * Pass the Success value into $hof
     * @param callable $hof ($value, $key, Attempt $container): null
     */
    abstract public function walk(callable $hof);
}

function __assertAttemptType($unknown) {
    if (!$unknown instanceof Attempt) {
        throw new \Exception ('return value must be an instance of Attempt!');
    }
    return $unknown;
}