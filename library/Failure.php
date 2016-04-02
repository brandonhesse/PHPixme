<?php

namespace Phpixme;

/**
 * Class Failure
 * @package PHPixme
 * Encloses an exception from a Attempt block, allowing it to be recovered from.
 * It will ignore any attempts to apply new success behaviors to the error state
 * contained within prior to any recovery attempts.
 */
class Failure extends Attempt
{
    private $err;

    /**
     * @inheritdoc
     */
    public function __construct(\Exception $exception)
    {
        $this->err = $exception;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        throw $this->err;
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
    public function flatMap(callable $hof)
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
    public function failed()
    {
        return Success($this->err);
    }

    /**
     * @inheritdoc
     */
    function isFailure()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    function isSuccess()
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
    public function recover(callable $rescueException)
    {
        return Attempt(function () use ($rescueException) {
            return $rescueException($this->err, $this);
        });
    }

    /**
     * @inheritdoc
     */
    public function recoverWith(callable $hof)
    {
        try {
            $result = $hof($this->err, $this);
        } catch (\Exception $e) {
            return Failure($e);
        }
        return __assertAttemptType($result);
    }

    /**
     * @inheritdoc
     */
    public function transform(callable $success, callable $failure)
    {
        try {
            $result = $failure($this->err, $this);
        } catch (\Exception $e) {
            return Failure($e);
        }
        return __assertAttemptType($result);
    }

    /**
     * @inheritdoc
     */
    public function walk(callable $hof)
    {
        // This space is intentionally left blank.
    }

}