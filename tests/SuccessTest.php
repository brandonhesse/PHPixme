<?php

namespace Phpixme;

class SuccessTest extends PhpixmeTestCase
{

    public function test_success_companion()
    {
        $this->assertStringEndsWith(
            '\Success'
            , Success::class
            , 'Ensure the constant ends with the function/class name'
        );
        $this->assertInstanceOf(
            Success::class,
            Success(false),
            'the companion function should return an instance of its class'
        );
    }

    public function test_is_status()
    {

        $success = Success(true);
        $this->assertTrue(
            $success->isSuccess()
            , 'it should be a success'
        );
        $this->assertFalse(
            $success->isFailure()
            , 'it should not be a failure'
        );
    }


    public function getProvider()
    {
        return [
            [null, 'should be able to store then retrieve null']
            , [new \stdClass(), 'should be able to store then retrieve another built in class']
            , [[], 'should be able to store then retrieve arrays']
            , [100, 'should be able to store then retrieve numbers']
            , ["Hi!", 'should be able to store then retrieve strings']
            , [Failure(new \Exception('Test Exception')), 'Should be able to contain and retrieve failures']
        ];
    }

    /**
     * @dataProvider getProvider
     */
    public function test_get($value, $message)
    {
        $this->assertTrue(
            $value === (Success($value)->get())
            , $message
        );
    }

    public function test_getOrElse($value = true, $default = false)
    {
        $this->assertTrue(
            $value === (Success($value)->getOrElse($default))
            , 'Success->getOrElse should be an its contents'
        );
    }

    public function test_orElse($value = true)
    {
        $instance = Success($value);
        $this->assertTrue(
            $instance === ($instance->orElse(function () use ($value) {
                return Success($value);
            }))
            , 'Success->orElse should be an identity'
        );
    }


    public function test_filter_callback($value = true)
    {
        $success = Success($value);
        $success->filter(function () use ($value, $success) {
            $this->assertTrue(
                3 === func_num_args()
                , 'Success->filter callback should receive 3 arguments'
            );
            $this->assertTrue(
                $value === func_get_arg(0)
                , 'Success->filter callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Success->filter callback $key should be defined'
            );
            $this->assertTrue(
                $success === func_get_arg(2)
                , 'Success->filter callback $container should be itself'
            );
            return true;
        });
    }

    public function test_filter($value = true)
    {
        $success = Success($value);
        $this->assertTrue(
            $success === ($success->filter(function () {
                return true;
            }))
            , 'Success->filter When a receiving a true should be an identity'
        );

        $false = function () {
            return false;
        };
        $this->assertInstanceOf(
            Failure::class
            , $success->filter($false)
            , 'Success->filter When receiving a false should become a Failure'
        );
    }


    function test_flatMap_callback($value = true)
    {
        $child = Success($value);
        $success = Success($child);
        $success->flatMap(function () use ($success, $child) {
            $this->assertTrue(
                3 === func_num_args()
                , 'Success->flatMap should pass three arguments to the callback'
            );
            $this->assertTrue(
                $child === func_get_arg(0)
                , 'Success->flatMap callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Success->flatMap callback $key should be defined'
            );
            $this->assertTrue(
                $success === func_get_arg(2)
                , 'Success->flatMap $container should be itself'
            );
            return true;
        });
    }


    /**
     * Ensure that flatMap throws an exception if the callback does not honor it's callback
     * @expectedException \Exception
     */
    function test_flatMap_contract_broken()
    {
        Success(true)->flatMap(function () {
        });
    }

    function test_flatMap_scenario_contains_success($value = true)
    {
        $child = Success($value);
        $success = Success($child);
        $flatten = function ($value) {
            return $value;
        };
        $this->assertTrue(
            $child === ($success->flatMap($flatten))
            , 'Success->flatMap should return its contained Success'
        );
    }

    function test_flatMap_scenario_contains_failure()
    {
        $flatten = function ($value) {
            return $value;
        };
        $this->assertInstanceOf(
            Failure::class
            , Success(Failure(new \Exception()))->flatMap($flatten)
            , 'Success->flatMap shouldn\'t care if the contents returned is a Failure'
        );
    }


    function test_flatten_scenario_success($value = true)
    {
        $child = Success($value);
        $parent = Success($child);
        $this->assertTrue(
            $child === ($parent->flatten())
            , 'Success->flatten should return its contained Success'
        );

    }

    public function test_flatten_scenario_contains_failure()
    {
        $child = Failure(new \Exception());
        $parent = Success($child);
        $this->assertTrue(
            $child === ($parent->flatten())
            , 'Success->flatten should return its contained Failure'
        );
    }

    /**
     * Ensure flatten calls an exception if the object violate's it own contract
     * @expectedException \Exception
     */
    function test_flatten_contract_broken($value = true)
    {
        Success($value)->flatten();
    }

    function test_failed($value = true)
    {
        $this->assertInstanceOf(
            Failure::class
            , Success($value)->failed()
            , 'Success->failed produces a Failure'
        );
    }


    function test_map_callback($value = true)
    {
        $success = Success($value);
        $success->map(function () use ($value, $success) {
            $this->assertTrue(
                3 === func_num_args()
                , 'Success->map callback should receive 3 arguments'
            );
            $this->assertTrue(
                $value === func_get_arg(0)
                , 'Success->map callback $value should be equal to what is contained'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Success->map callback $key should be defined, even if its not so useful'
            );
            $this->assertTrue(
                $success === func_get_arg(2)
                , 'Success->map callback $container should be itself'
            );
            return true;
        });
    }

    /**
     * @depends test_get
     */
    function test_map($value = true)
    {
        $success = Success($value);
        $one = function () {
            return 'one';
        };
        $result = $success->map($one);
        $this->assertInstanceOf(
            Success::class
            , $result
            , 'Success->map should stay a Success'
        );
        $this->assertFalse(
            $success === $result
            , 'Success->map should not return Success of the same instance'
        );
        $this->assertTrue(
            'one' === ($result->get())
            , 'Success->map should have the correct results'
        );
    }

    public function test_recover($value = true)
    {
        $success = Success($value);
        $this->assertTrue(
            $success === ($success->recover(function () {
            }))
            , 'Success->Recover is an identity'
        );
    }

    public function test_recoverWith($value = true)
    {
        $success = Success($value);
        $this->assertTrue(
            $success === ($success->recoverWith(function () {
            }))
            , 'Success->RecoverWith is an identity'
        );
    }

    public function test_toArray($value = true)
    {

        $result = Success($value)->toArray();
        $this->assertTrue(
            $result['success']
            , 'Success->toArray method should return an array ["success" => contents]'
        );
        $this->assertNotTrue(
            isset($result['failure'])
            , 'Success->toArray results should not contain a failure key'
        );
    }

    public function test_toMaybe($value = true)
    {
        $result = Success($value)->toMaybe();
        $this->assertInstanceOf(
            Some::class
            , $result
            , 'Success->toMaybe should result in Some'
        );
        $this->assertTrue(
            $value === ($result->get())
            , 'Success->toMaybe resultant Some should contain the same value'
        );
    }


    public function test_transform_callback($value = true)
    {
        $success = Success($value);
        $success->transform(function () use ($value, $success) {
            $this->assertTrue(
                2 === func_num_args()
                , 'Success->transform signature contains two arguments'
            );
            $this->assertTrue(
                $value === func_get_arg(0)
                , 'Success->transform callback $value its contents'
            );
            $this->assertTrue(
                $success === func_get_arg(1)
                , 'Success->transform $container should be itself'
            );
            return $success;
        }
            , function () {
                throw new \Exception('Success->transform should never run the failure path!');
            }
        );
    }

    public function test_transform_scenario_success_to_success($value = true)
    {
        $thing1 = Success($value);
        $thing2 = Success($value);
        $noop = function () {
        };
        $switchToThing2 = function () use ($thing2) {
            return $thing2;
        };
        $this->assertTrue(
            $thing2 === $thing1->transform($switchToThing2, $noop)
            , 'Success->transform should return the results of its success path callback'
        );


    }

    public function test_transform_scenario_success_to_failure($value = true)
    {
        $failure = Failure(new \Exception('test'));
        $makeFailure = function () use ($failure) {
            return $failure;
        };
        $noop = function () {
        };
        $this->assertTrue(
            $failure === Success($value)->transform($makeFailure, $noop)
            , 'Success->transform Success callback type should be able to be a Failure'
        );
    }

    /**
     * Ensure that Transform throws an exception if the callbacks violate the contract
     * @expectedException \Exception
     */
    public function test_transform_broken_contract()
    {
        Success(true)->transform(function () {
        }, function () {
        });
    }


    public function test_walk_callback($value = true)
    {
        $success = Success($value);
        $success->walk(function () use ($value, $success) {
            $this->assertTrue(
                3 === func_num_args()
                , 'Success->walk should pass three arguments'
            );
            $this->assertTrue(
                $value === func_get_arg(0)
                , 'Success->walk callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Success->walk callback $key should be defined'
            );
            $this->assertTrue(
                $success === func_get_arg(2)
                , 'Success->walk should pass container as itself'
            );
        });
    }

    public function test_walk($value = true)
    {
        $success = Success($value);
        $ran = 0;
        $success->walk(function () use (&$ran) {
            $ran += 1;
        });
        $this->assertTrue(
            1 === $ran
            , 'Success->walk should of ran only one time'
        );
    }


}
