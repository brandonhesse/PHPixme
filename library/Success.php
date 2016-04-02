<?php

namespace Phpixme;

/**
 * Class Success
 * @package PHPixme
 * Contains the results of a successful Attempt block, allowing for successful
 * behaviors prior to the block to be executed.
 */
class Success extends Attempt
{
    private $value = null;

    /**
     * @inheritdoc
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $hof)
    {
        try {
            return ($hof($this->value, 0, $this)) ?
                $this
                : Failure(new \Exception('$value did not meet criteria.'));
        } catch (\Exception $e) {
            return Failure($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function flatMap(callable $hof)
    {
        try {
            $result = $hof($this->value);
        } catch (\Exception $e){
            return Failure($e);
        }
        return __assertAttemptType($result);
    }

    public function flatten()
    {
        return __assertAttemptType($this->value);
    }

    /**
     * @inheritdoc
     */
    public function failed()
    {
        return Failure(new \Exception('Success.failed is an unsupported action.'));
    }

    /**
     * @inheritdoc
     */
    public function isFailure()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $hof)
    {
        return new static($hof($this->value, 0, $this));
    }

    /**
     * @inheritdoc
     */
    public function recover(callable $rescueException)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function recoverWith(callable $hof)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function transform(callable $success, callable $failure)
    {
        try {
            $result = $success($this->value, $this);
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
        $hof($this->value, 0, $this);
    }
}
