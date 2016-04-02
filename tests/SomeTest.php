<?php
namespace Phpixme;

class SomeTest extends PhpixmeTestCase
{
    public function test_Some_companion($value = true)
    {
        $this->assertStringEndsWith(
            '\Some'
            , Some::class
            , 'Ensure the constant ends with the function/class name'
        );
        $this->assertInstanceOf(
            Some::class,
            Some($value)
        );
    }

    public function test_Some_static_of($value = true)
    {
        $this->assertInstanceOf(
            Some::class,
            Some::of($value)
        );
    }

    public function test_Some_static_from($value = [true])
    {
        $this->assertInstanceOf(
            Some::class,
            Some::from($value)
        );
    }

    public function test_contains($value = true, $notValue = false)
    {
        $some = Some($value);
        $this->assertTrue(
            $some->contains($value)
            , 'Some->contains should contain the value'
        );
        $this->assertFalse(
            $some->contains($notValue)
            , 'Some->contains should not contain anything other than itself'
        );
    }

    public function test_exists($value = true)
    {
        $getValue = function ($x) use ($value) {
            return $x === $value;
        };
        $getNotValue = function ($x) use ($value) {
            return $x !== $value;
        };
        $some = Some($value);
        $this->assertTrue(
            $some->exists($getValue)
            , 'Some->exists should return true if the predicate returned true'
        );
        $this->assertFalse(
            $some->exists($getNotValue)
            , 'Some->exists should return false if the predicate returned false'
        );
    }

    public function test_get($value = true)
    {
        $this->assertTrue(
            $value === (Some($value)->get())
            , 'Some->get should return its contents'
        );
    }

    public function test_getOrElse($value = true, $default = false)
    {
        $this->assertTrue(
            $value === (Some($value)->getOrElse($default))
            , 'Some->getOrElse should return its contents and ignore the default'
        );
    }

    public function test_isDefined($value = true)
    {
        $this->assertTrue(
            Some($value)->isDefined()
            , 'Some->isDefined should return true'
        );
    }

    public function test_orNull($value = true)
    {
        $contained = Some($value)->orNull();
        if (is_null($value)) {
            $this->assertNull(
                $contained
                , 'Some->orNull when containing null should return null'
            );
        } else {
            $this->assertNotNull(
                $contained
                , 'Some->orNull should return its contents'
            );
        }
    }

    public function test_orElse($value = true)
    {
        $some = Some($value);
        $this->assertTrue(
            $some === ($some->orElse(function () {
                throw new \Exception('Some->orElse callback should never run');
            }))
            , 'Some->orElse is an identity'
        );
    }

    public function test_toSeq($value = true)
    {
        $this->assertInstanceOf(
            Seq::class
            , Some($value)->toSeq()
            , 'Some->toSeq should produce a Sequence'
        );
    }


    public function test_fold_callback($value = true, $startValue = null)
    {
        $some = Some($value);
        $some->fold(function ($lastVal) use ($startValue, $value, $some) {
            $this->assertTrue(
                4 === func_num_args()
                , 'Some->fold callback should receive four arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $startValue
                , 'Some->fold callback $prevVal should be the $startValue'
            );
            $this->assertTrue(
                func_get_arg(1) === $value
                , 'Some->fold callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(2)
                , 'Some->fold callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(3) === $some
                , 'Some->fold callback $container should be itself'
            );

            return $lastVal;
        }, $startValue);
    }

    public function test_fold_scenario_add($value = 1, $startVal = 1)
    {
        $add = function ($x, $y) {
            return $x + $y;
        };
        $this->assertTrue(
            ($value + $startVal) === (Some($value)->fold($add, $startVal))
            , 'The fold should be able to preform a simple add on a single length item'
        );
    }

    public function test_reduce($value = true)
    {
        $this->assertTrue(
            $value === (Some($value)->reduce(function () {
                throw new \Exception('Some->reduce callback should never run!');
            }))
            , 'Some->reduce should produce its contents'
        );
    }

    public function test_map_callback($value = true)
    {
        $some = Some($value);
        $some->map(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->map callback should receive three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->map callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->map callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->map callback $container should be itself'
            );
        });
    }

    public function test_map($value = true)
    {
        $original = Some($value);
        $duplicate = $original->map(function ($value) {
            return $value;
        });
        $this->assertInstanceOf(
            Some::class
            , $duplicate
            , 'Some->map function should remain a Some'
        );
        $this->assertTrue(
            $original !== $duplicate
            , 'Some->map should return an different Some'
        );
    }

    public function test_flatMap_callback($value = true)
    {
        $some = Some($value);
        $some->flatMap(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->flatMap callback should receive three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->flatMap callback $value should its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->flatMap callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->flatMap callback $container should be itself'
            );
            return Some($value);
        });
    }

    /**
     * Ensure the contract is help for flatmap that the container returned will be what was contained
     * @expectedException \Exception
     */
    public function test_flatMap_contract_broken($value = true)
    {
        Some($value)->flatMap(function ($value) {
            return $value;
        });
    }

    public function test_flatMap($value = true)
    {
        $some2 = Maybe($value);
        $some1 = Some($some2);

        $this->assertTrue(
            $some2 === ($some1->flatMap(function ($value) {
                return $value;
            }))
            , 'Some->flatMap ran with the identity function return its contents of a Maybe type'
        );
    }

    public function test_flatten($value = true)
    {
        $some2 = Maybe($value);
        $some1 = Some($some2);
        $this->assertTrue(
            ($some1->flatten()) === $some2
            , 'Some->flatten should return its contents of Maybe'
        );
    }

    /**
     * @expectedException \Exception
     */
    public function test_flatten_contract_broken()
    {
        Some(true)->flatten();
    }

    public function test_filter_callback($value = true)
    {
        $some = Some($value);
        $some->filter(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->filter callback should receive three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->filter callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->filter callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->filter callback $container should be itself'
            );
            return true;
        });
    }

    public function test_filter_scenario_isArray($value = true, $expected = None::class)
    {
        $some = Some($value);
        $results = $some->filter(function ($val) {
            return is_array($val);
        });
        $this->assertInstanceOf(
            $expected
            , $results
            , 'Some->filter application of is_array on ' . json_encode($value) . ' should of been ' . json_encode($expected)
        );
        if ($expected === Some::class) {
            $this->assertTrue(
                $some === $results
                , 'Some->filter where callback results are true is an identity'
            );
        }
    }

    public function test_filterNot_callback($value = true)
    {
        $some = Some($value);
        $some->filterNot(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->filterNot callback should have three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->filter callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->filterNot $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->filterNot $contents should be itself'
            );
            return true;
        });
    }

    public function test_filterNot_scenario_isArray($value = true, $expected = Some::class)
    {
        $some = Some($value);
        $results = $some->filterNot(function ($val) {
            return is_array($val);
        });
        $this->assertInstanceOf(
            $expected
            , $results
            , 'Some->filterNot application of is_array on ' . json_encode($value) . ' should of been ' . json_encode($expected)
        );
        if ($expected === Some::class) {
            $this->assertTrue(
                $some === $results
                , 'Some->filterNot callback is unsuccessful, the result is an identity'
            );
        }
    }

    public function test_forAll_callback($value = true)
    {
        $some = Some($value);
        $some->forAll(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->forAll callback receives three augments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->forAll callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->forAll callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->forAll callback $container is itself'
            );
            return true;
        });
    }

    public function test_forAll_scenario_isArray($value = true, $expected = false)
    {
        $isArray = function ($value) {
            return is_array($value);
        };
        $this->assertTrue(
            $expected === Some($value)->forAll($isArray)
            , 'Some->forAll application of is_array on ' . json_encode($value) . ' should of been ' . json_encode($expected)
        );
    }

    public function test_forNone_callback($value = true)
    {
        $some = Some($value);
        $some->forNone(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->forNone callback should receive three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->forNone callback $value should its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->forNone callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->forNone callback $container should be itself'
            );
            return true;
        });
    }

    public function test_forNone_scenario_isArray($value = true, $expected = true)
    {
        $isArray = function ($value) {
            return is_array($value);
        };
        $this->assertTrue(
            $expected === Some($value)->forNone($isArray)
            , 'Some->forNone application of is_array on ' . json_encode($value) . ' should of been ' . json_encode($expected)
        );
    }

    public function test_forSome_callback($value = true)
    {
        $some = Some($value);
        $some->forSome(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->forSome callback should receive three arguments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->forSome callback $value its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->forSome callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->forSome callback $container should be itself'
            );
            return true;
        });
    }

    public function test_forSome_scenario_isArray($value = true, $expected = false)
    {
        $isArray = function ($value) {
            return is_array($value);
        };
        $this->assertTrue(
            $expected === Some($value)->forSome($isArray)
            , 'Some->forSome application of is_array on ' . json_encode($value) . ' should of been ' . json_encode($expected)
        );
    }

    public function test_walk_callback($value = true)
    {
        $some = Some($value);
        $some->forSome(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->walk callback should receive three augments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->walk callback $value should be its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->walk callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->walk callback $container should be itself'
            );
        });
    }

    public function test_toArray($value = true)
    {
        $arr = Some($value)->toArray();
        $this->assertTrue(
            is_array($arr)
            , 'Some->toArray should yield an array'
        );
        $this->assertTrue(
            1 === count($arr)
            , 'Some->toArray results should be one long'
        );
        $this->assertTrue(
            $value === $arr[0]
            , 'Some->toArray result contents should be the same as itself'
        );
    }

    public function test_find_callback($value = true)
    {
        $some = Some($value);
        $some->find(function () use ($value, $some) {
            $this->assertTrue(
                func_num_args() === 3
                , 'Some->find callback should receive three arugments'
            );
            $this->assertTrue(
                func_get_arg(0) === $value
                , 'Some->find callback $value should be equal to its contents'
            );
            $this->assertNotFalse(
                func_get_arg(1)
                , 'Some->find callback $key should be defined'
            );
            $this->assertTrue(
                func_get_arg(2) === $some
                , 'Some->find callback $container should be itself'
            );
            return true;
        });
    }

    public function test_find($value = true)
    {
        $some = Some($value);
        $positiveResult = $some->find(function ($x) use ($value) {
            return $x === $value;
        });
        $negativeResult = $some->find(function ($x) use ($value) {
            return $x !== $value;
        });

        $this->assertInstanceOf(
            Some::class
            , $positiveResult
            , 'Some->Find on a true callback should return a Some'
        );
        $this->assertTrue(
            $positiveResult === $some
            , 'Some->find on a truly returning callback should yield an identity'
        );
        $this->assertInstanceOf(
            None::class
            , $negativeResult
            , 'Some->find a falsely returning callback yields a None'
        );

    }

    function test_forEach($value = true)
    {
        $iter = Some($value);
        $run = 0;
        foreach ($iter as $key => $val) {
            $run += 1;
            $this->assertTrue(
                $value === $val
                , 'The current of Some should result its contents. Expected ' . json_encode($value) . ' but got ' . json_encode($val)
            );
        }

        $this->assertTrue(
            $run === 1
            , 'Some should always have one value to iterate over before halting. Ran ' . $run . ' times.'
        );
    }

    public function test_count($value = true)
    {
        $this->assertEquals(
            1
            , Some($value)->count()
            , 'Some->count should always equal 1'
        );
    }
}
