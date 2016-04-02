<?php

namespace Phpixme;
const Closure = '\Closure';
class FunctionalTest extends PhpixmeTestCase
{
    public function test_curry()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $param3 = curry(3, $countArgs);
        $this->assertInstanceOf(
            Closure
            , $param3
            , 'Curry should return a closure'
        );
        $this->assertInstanceOf(
            Closure
            , $param3(1)
            , 'Curried functions should be still a closure when partially applied'
        );
        $this->assertEquals(
            3
            , $param3(1, 2, 3)
            , 'Curried functions should run when the minimum arguments are applied'
        );
        $this->assertEquals(
            4
            , $param3(1, 2, 3, 4)
            , 'Curried functions may pass more than the minimum arity is passed'
        );
        $this->assertEquals(
            3
            , $param3(1)->__invoke(2)->__invoke(3)
            , 'Curried functions should be able to be chained'
        );
        $curry2 = curry(2);
        $this->assertInstanceOf(
            Closure
            , $curry2
            , 'The curry function itself should be curried'
        );
        $this->assertInstanceOf(
            Closure
            , $curry2($countArgs)
            , 'The partially applied curry function should produce a closure'
        );
        $this->assertEquals(
            2
            , $curry2($countArgs)->__invoke(1, 2)
            , 'The partially applied version of curry should behave just like the non-partially applied one'
        );
    }

    public function test_nAry()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $this->assertInstanceOf(
            Closure
            , nAry(1)
            , 'nAry should be partially applied'
        );
        $this->assertEquals(
            1
            , nAry(1)->__invoke($countArgs)->__invoke(1, 2, 3)
            , 'nAry Partially applied should still produce a wrapped function that eats arguments'
        );
        $this->assertInstanceOf(
            Closure
            , nAry(1, $countArgs)
            , 'nAry fully applied should produce a closure'
        );
        $this->assertEquals(
            1
            , nAry(1, $countArgs)->__invoke(1, 2, 3, 4)
            , 'fully applied should still work the same as partially applied, eating arguments'
        );
    }

    public function test_unary()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $this->assertInstanceOf(
            Closure
            , unary($countArgs)
            , 'unary should return a closure'
        );
        $this->assertEquals(
            1
            , unary($countArgs)->__invoke(1, 2, 3)
            , 'Unary should eat all but one argument'
        );
    }

    public function test_binary()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $this->assertInstanceOf(
            Closure
            , binary($countArgs)
            , 'binary should return a closure'
        );

        $this->assertEquals(
            2
            , binary($countArgs)->__invoke(1, 2, 3)
            , 'binary should eat all but two arguments'
        );
    }

    public function test_ternary()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $this->assertInstanceOf(
            Closure
            , ternary($countArgs)
            , 'ternary should return a closure'
        );

        $this->assertEquals(
            3
            , ternary($countArgs)->__invoke(1, 2, 3, 4)
            , 'ternary should eat all but three arguments'
        );
    }

    public function test_nullary()
    {
        $countArgs = function () {
            return func_num_args();
        };
        $this->assertInstanceOf(
            Closure
            , nullary($countArgs)
            , 'nullary should return a closure'
        );

        $this->assertEquals(
            0
            , nullary($countArgs)->__invoke(1, 2, 3, 4)
            , 'nullary should eat all arguments'
        );
    }

    public function test_flip()
    {
        $getArgs = function () {
            return func_get_args();
        };
        $this->assertInstanceOf(
            Closure
            , flip($getArgs)
            , 'Flip should return a closure'
        );

        $this->assertEquals(
            [2, 1, 3, 4, 5]
            , flip($getArgs)->__invoke(1, 2, 3, 4, 5)
            , 'Flip should flip the first two arguments'
        );
        $this->assertInstanceOf(
            Closure
            , flip($getArgs)->__invoke(1)
            , 'Flip partially applied should return a closure'
        );

        $this->assertEquals(
            [2, 1, 3, 4, 5]
            , flip($getArgs)->__invoke(1)->__invoke(2, 3, 4, 5)
            , 'Flip partially applied should return the flipped arguments'
        );

    }

    public function test_combine()
    {

        $this->assertInstanceOf(
            \Closure::class
            , combine('json_encode', 'array_reverse')
            , 'combine should return a closure'
        );

        $this->assertInstanceOf(
            \Closure::class
            , combine('json_encode')
            , 'combine should be a curried function'
        );

        $array = [1, 2, 3];
        $this->assertEquals(
            json_encode(array_reverse($array))
            , combine('json_encode')->__invoke('array_reverse')->__invoke($array)
            , 'combine should be able to chain the outputs to produce hof results'
        );
    }

    public function test_K_estrel($value = true, $notValue = false)
    {
        $this->assertInstanceOf(
            Closure
            , K($value)
            , 'K should return a closure'
        );
        $this->assertEquals(
            $value
            , K($value)->__invoke($notValue)
            , 'K resultant closure should return the constant that has been closed'
        );
    }

    public function test_KI_te($value = true, $notValue = false)
    {

        $this->assertInstanceOf(
            Closure
            , KI($value)
            , 'KI should return a closure'
        );
        $this->assertEquals(
            $notValue
            , KI($value)->__invoke($notValue)
            , 'K resultant closure should ignore the constant and return the argument it recieves'
        );
    }

    public function test_I_diot_bird($value = true)
    {
        $this->assertEquals(
            $value
            , I($value)
            , 'The notoriously simple idiot bird proves useful in unusual places'
        );
    }

    public function test_S_tarling($value = true)
    {
        $this->assertInstanceOf(
            \Closure::class
            , S('I')
            , 'Starling should be able to be partially applied'
        );
        $this->assertEquals(
            $value
            , S('K')->__invoke(K($value))->__invoke($value)
            , 'S(K, K($value))($value) === $value is one of the more basic proofs to Starling'
        );

    }

    public function test_S_tarling_scenario_tupleMaker($array = [1, 2, 3, 4])
    {
        // Test to see if we can fix array_map through starling to get the key with the value
        $kvTupple = function ($v, $k) {
            return [$k, $v];
        };
        $kvMap = ternary('array_map')->__invoke($kvTupple);
        $this->assertEquals(
            array_map($kvTupple, $array, array_keys($array))
            , S($kvMap, 'array_keys')->__invoke($array)
        );
    }


    public function foldCallbackProvider()
    {
        return [
            'array callback' => [
                [1], 1, 0
            ]
            , 'traversable callback' => [
                new \ArrayIterator([1]), 1, 0
            ]
            , 'natural interface callback' => [
                Seq([1]), 1, 0
            ]
        ];
    }

    /**
     * @dataProvider foldCallbackProvider
     */
    public function test_fold_callback($value, $expVal, $expKey)
    {
        $startVal = 1;
        fold(function () use ($startVal, $value, $expVal, $expKey) {
            $this->assertEquals(
                4
                , func_num_args()
                , 'fold callback should receive four arguments'
            );
            $this->assertEquals(
                $startVal
                , func_get_arg(0)
                , 'fold callback $prevVal should equal startValue'
            );
            $this->assertEquals(
                $expVal
                , func_get_arg(1)
                , 'fold callback $value should equal to expected value'
            );
            $this->assertEquals(
                $expKey
                , func_get_arg(2)
                , 'fold callback $key should equal to expected key'
            );
            if (is_object($value)) {
                $this->assertTrue(
                    $value === func_get_arg(3)
                    , 'fold callback $container should be the same instance as the object'
                );
            } else {
                $this->assertEquals(
                    $value
                    , func_get_arg(3)
                    , 'fold callback $container should equal to the array'
                );
            }

            return func_get_arg(0);
        }, $startVal, $value);
    }

    public function test_fold($value = 1, $array = [1, 2, 3, 4])
    {
        $this->assertInstanceOf(
            Closure
            , fold('I', $value)
            , 'fold when partially applied should return a closure'
        );
        $this->assertEquals(
            $value
            , fold('I', $value)->__invoke($array)
            , 'An idiot applied to fold should always return the start value'
        );
        $this->assertEquals(
            $array[count($array) - 1]
            , fold(flip('I'), $value, $array)
            , 'The flipped idiot applied to reduce should always return the last unless empty'
        );
    }

    public function foldScenarioProvider()
    {
        $add = function ($a, $b) {
            return $a + $b;
        };
        return [
            'add simple empty array' => [
                []
                , 0
                , $add
                , 0
            ]
            , 'add simple S[]' => [
                Seq::of()
                , 0
                , $add
                , 0
            ]
            , 'add simple None' => [
                None()
                , 0
                , $add
                , 0
            ]
            , 'ArrayObject[]' => [
                new \ArrayIterator([])
                , 0
                , $add
                , 0
            ]
            , 'add 1+2+3' => [
                [1, 2, 3]
                , 0
                , $add
                , 6
            ]
            , 'add S[1,2,3]' => [
                Seq::of(1, 2, 3)
                , 0
                , $add
                , 6
            ]
            , 'Some(2)+2' => [
                Some(2)
                , 2
                , $add
                , 4
            ]
            , 'add ArrayObject[1,2,3]' => [
                new \ArrayIterator([1, 2, 3])
                , 0
                , $add
                , 6
            ]
        ];
    }

    /**
     * @dataProvider foldScenarioProvider
     */
    public function test_fold_scenario($arrayLike, $startVal, $action, $expected)
    {
        $this->assertEquals(
            $expected
            , fold($action, $startVal, $arrayLike)
        );
    }


    public function test_reduce($array = [1, 2, 3, 4])
    {
        $this->assertInstanceOf(
            Closure
            , reduce('I')
            , 'reduce when partially applied should return a closure'
        );
        $this->assertEquals(
            $array[0]
            , reduce('I')->__invoke($array)
            , 'An idiot applied to fold should always return the start value'
        );
        $this->assertEquals(
            $array[count($array) - 1]
            , reduce(flip('I'), $array)
            , 'The flipped idiot applied to reduce should always return the last'
        );
    }

    public function reduceUndefinedBehaviorProvider()
    {
        return [
            '[]' => [[]]
            , 'None' => [None()]
            , 'S[]' => [Seq::of()]
            , 'ArrayItterator[]' => [new \ArrayIterator([])]
        ];
    }

    /**
     * @dataProvider reduceUndefinedBehaviorProvider
     * @expectedException \Exception
     */
    public function test_reduce_contract_violation($arrayLike = [])
    {
        reduce('I', $arrayLike);
    }

    public function reduceCallbackProvider()
    {
        return [
            'array callback' => [
                [1, 2], 1, 2, 1
            ]
            , 'traversable callback' => [
                new \ArrayIterator([1, 2]), 1, 2, 1
            ]
            , 'natural interface callback' => [
                Seq::of(1, 2), 1, 2, 1
            ]
        ];
    }

    /**
     * @dataProvider reduceCallbackProvider
     */
    public function test_reduce_callback($arrayLike, $firstVal, $expVal, $expKey)
    {
        reduce(function () use ($firstVal, $expVal, $expKey, $arrayLike) {
            $this->assertEquals(
                4
                , func_num_args()
                , 'reduce callback should receive four arguments'
            );
            $this->assertEquals(
                $firstVal
                , func_get_arg(0)
                , 'reduce callback $prevVal should equal startValue'
            );
            $this->assertEquals(
                $expVal
                , func_get_arg(1)
                , 'reduce callback should equal to expected value'
            );
            $this->assertEquals(
                $expKey
                , func_get_arg(2)
                , 'reduce callback $key should equal to expected key'
            );
            if (is_object($arrayLike)) {
                $this->assertTrue(
                    $arrayLike === func_get_arg(3)
                    , 'reduce callback $container should be the same instance as the source data'
                );
            } else {
                $this->assertEquals(
                    $arrayLike
                    , func_get_arg(3)
                    , '$container should equal to the array being reduced'
                );
            }

            return func_get_arg(0);
        }, $arrayLike);
    }

    public function reduceScenarioProvider()
    {
        $add = function ($a, $b) {
            return $a + $b;
        };
        return [
            'add 1' => [
                [1]
                , $add
                , 1
            ]
            , 'add S[1]' => [
                Seq::of(1)
                , $add
                , 1
            ]

            , 'add ArrayObject[1]' => [
                new \ArrayIterator([1])
                , $add
                , 1
            ]
            , 'add Some(2)' => [
                Some(2)
                , $add
                , 2
            ]
            , 'add 1+2+3' => [
                [1, 2, 3]
                , $add
                , 6
            ]
            , 'add S[1,2,3]' => [
                Seq::of(1, 2, 3)
                , $add
                , 6
            ]

            , 'add ArrayObject[1,2,3]' => [
                new \ArrayIterator([1, 2, 3])
                , $add
                , 6
            ]
        ];
    }

    /**
     * @dataProvider reduceScenarioProvider
     */
    public function test_reduce_scenario($arrayLike, $action, $expected)
    {
        $this->assertEquals(
            $expected
            , reduce($action, $arrayLike)
        );
    }

    public function test_map($array = [1, 2, 3])
    {
        $this->assertInstanceOf(
            Closure
            , map('I')
            , 'map when partially applied should return a closure'
        );
        $result = map('I')->__invoke($array);
        $this->assertEquals(
            $array
            , $result
            , 'map applied with idiot should produce a functionally identical array'
        );
        $result[0] += 1;
        $this->assertNotEquals(
            $array
            , $result
            , 'map applied with idiot should not actually be the same instance of array'
        );
    }

    public function mapCallbackProvider()
    {
        return [
            '[1]' => [
                [1], 1, 0
            ]
            , 'S[1]' => [
                Seq::of(1), 1, 0
            ]
            , 'Some(1)' => [
                Some::of(1), 1, 0
            ]
            , 'ArrayItterator[1]' => [
                new \ArrayIterator([1]), 1, 0
            ]
        ];
    }

    /**
     * @dataProvider mapCallbackProvider
     */
    public function test_map_callback($arrayLike, $expVal, $expKey)
    {
        map(function () use ($arrayLike, $expVal, $expKey) {
            $this->assertTrue(
                3 === func_num_args()
                , 'map callback should receive three arguments'
            );
            $this->assertEquals(
                $expVal
                , func_get_arg(0)
                , 'map callback $value should be equal to the value expected'
            );
            $this->assertEquals(
                $expKey
                , func_get_arg(1)
                , 'map callback $key should be defined'
            );
            if (is_object($arrayLike)) {
                $this->assertTrue(
                    $arrayLike === func_get_arg(2)
                    , 'map callback $container should be the same instance as the source data being mapped'
                );
            } else {
                $this->assertEquals(
                    $arrayLike
                    , func_get_arg(2)
                    , 'map callback $container should equal to the array being mapped'
                );
            }
        }, $arrayLike);
    }

    public function mapScenarioProvider()
    {
        $x2 = function ($value) {
            return $value * 2;
        };
        return [
            '[1,2] * 2' => [
                [1, 2]
                , $x2
                , [2, 4]
            ]
            , 'ArrayIterator[1,2] * 2' => [
                new \ArrayIterator([1, 2])
                , $x2
                , [2, 4]
            ]
            , 'S[1,2] * 2' => [
                Seq::of(1, 2)
                , $x2
                , Seq::of(2, 4)
            ]
            , 'Some(1) *2' => [
                Some(1)
                , $x2
                , Some(2)
            ]
            , 'None * 2' => [
                None()
                , $x2
                , None()
            ]
            , '[1,2,3] to string' => [
                [1, 2, 3]
                , function ($value, $key) {
                    return "$key => $value";
                }
                , ['0 => 1', '1 => 2', '2 => 3']
            ]
        ];
    }

    /**
     * @dataProvider mapScenarioProvider
     */
    public function test_map_scenario($arrayLike, $hof, $expected)
    {
        $this->assertEquals($expected
            , map($hof, $arrayLike)
            , 'map on array like should have the expected resultant'
        );
    }

    public function callWithProvider()
    {
        return [
            'Object' => [new TestClass()]
            , 'Array' => [[
                'getArgs' => function () {
                    return func_get_args();
                }
                , 'countArgs' => function () {
                    return func_num_args();
                }
            ]]
        ];
    }

    /**
     * @dataProvider callWithProvider
     */
    public function test_callWith($container)
    {
        $this->assertInstanceOf(
            \Closure::class
            , callWith('')
            , 'callWith partially applied should be a closure'
        );
        $this->assertInstanceOf(
            \Closure::class
            , callWith('getArgs')->__invoke($container)
            , 'callWith when fully applied should be a closure'
        );
        $this->assertEquals(
            [1, 2, 3]
            , callWith('getArgs', $container)->__invoke(1, 2, 3)
            , 'callWith should invoke the function with the returned closure'
        );
        $this->assertEquals(
            4,
            callWith('countArgs')->__invoke($container)->__invoke(1, 2, 3, 4)
            , 'callWith when partially applied should invoke the function with the returned closure'
        );
    }

    /**
     * @expectedException  \InvalidArgumentException
     * @dataProvider callWithProvider
     */
    public function test_callWithNonFunction ($container) {
        callWith('404', $container)->__invoke(1, 2, 3, 4);
    }

    public function test_puckObjectWith()
    {
        $object = new TestClass();
        $this->assertInstanceOf(
            Closure
            , pluckObjectWith('')
            , 'pluckObjectWith should be able to be partially applied'
        );
        $this->assertTrue(
            pluckObjectWith('value')->__invoke($object)
            , 'pluckObjectWith\'s yielded closure should retrieve the value of the property on object when applied'
        );
    }

    public function test_puckArrayWith()
    {
        $array = [1, 2, 3];
        $this->assertInstanceOf(
            Closure
            , pluckArrayWith('')
            , 'pluckArrayWith should be able to be partially applied'
        );
        $this->assertEquals(
            $array[0]
            , pluckArrayWith(0)->__invoke($array)
            , 'pluckArrayWith\'s yielded closure should retrieve the value of the property on object when applied'
        );
    }
}

/**
 * Class TestClass
 * @package Phpixme
 * A class to assist in testing properties of object functions
 */
class TestClass
{
    public $value = true;

    public function getArgs()
    {
        return func_get_args();
    }

    public function countArgs()
    {
        return func_num_args();
    }
}