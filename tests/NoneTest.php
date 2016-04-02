<?php

namespace Phpixme;

class NoneTest extends PhpixmeTestCase
{
    public function test_None_companion()
    {

        $this->assertStringEndsWith(
            '\None'
            , None::class
            , 'Ensure the constant ends with the function/class name'
        );
        $this->assertInstanceOf(
            None::class
            , None()
            , 'It should return an instance of none'
        );
    }

    public function test_None_aspect_Singleton()
    {
        $this->assertTrue(
            None() === None()
            , 'None should be a singleton'
        );
    }

    public function test_None_static_getInstance()
    {
        $this->assertInstanceOf(
            None::class
            , None::getInstance()
            , 'Get Instance should return its instance of itself'
        );
    }

    public function test_None_aspects_traversable()
    {
        $this->assertInstanceOf(
            '\Traversable'
            , None()
            , 'None should be a traversable'
        );
    }

    public function test_None_aspects_Natural_Transformation()
    {
        $this->assertInstanceOf(
            'Phpixme\NaturalTransformationInterface'
            , None()
            , 'It should have implemented natural transformations'
        );
    }

    public function test_static_of()
    {
        $this->assertTrue(
            None() === (None::of($this))
            , 'Of on None is its singleton'
        );
    }

    public function test_static_from()
    {
        $this->assertTrue(
            None() === (None::from([$this]))
            , 'From on None is its singleton'
        );

    }

    public function test_contains()
    {
        $this->assertFalse(
            None()->contains(1)
            , 'None should contain nothing'
        );
    }

    public function test_exists()
    {
        $this->assertFalse(
            None()->exists(
                function () {
                    throw new \Exception('The callback should not run');
                }
            )
        );
    }

    public function test_forAll()
    {
        $this->assertTrue(
            None()->forAll(function () {
                throw new \Exception('The callback should not run');
            })
            , 'The result of forAll on None should be true'
        );
    }

    /**
     * @expectedException \Exception
     */
    public function test_get()
    {
        None()->get();
    }

    public function test_getOrElse()
    {
        $this->assertTrue(
            None()->getOrElse(true)
            , 'None should return getOrElse default'
        );
    }

    public function test_isDefined()
    {
        $this->assertFalse(
            None()->isDefined()
            , 'None is not considered defined'
        );
    }

    public function test_orNull()
    {
        $this->assertNull(
            None()->orNull()
            , 'orNull should be Null on None'
        );
    }

    public function test_orElse()
    {
        $results = Maybe(10);
        $getResults = function () use ($results) {
            return $results;
        };
        $this->assertTrue(
            $results === (None()->orElse($getResults))
            , 'orElse on None should use the default HoF'
        );
    }

    /**
     * Ensure the contract on orElse is maintained
     * @expectedException \Exception
     */
    public function test_orElse_contract_broken()
    {
        None()->orElse(function () {
        });
    }

    public function test_toSeq()
    {
        $this->assertInstanceOf(
            Seq::class
            , None()->toSeq()
            , 'toSeq should produce a Sequence Type'
        );
    }

    /**
     * @expectedException \Exception
     */
    public function test_reduce()
    {
        None()->reduce(function () {
        });
    }

    public function test_fold()
    {
        $startVal = true;
        $this->assertTrue(
            $startVal === (None()->fold(function () {
                throw new \Exception('This should not run!');
            }, $startVal))
            , 'Folds on empty collections should return start values'
        );
    }

    public function test_map()
    {
        $none = None();
        $this->assertTrue(
            $none === ($none->map(function () {
                throw new \Exception('This should not run!');
            }))
            , 'Map on None is an identity'
        );
    }

    public function filter()
    {
        $none = None();
        $this->assertTrue(
            $none === ($none->filter(function () {
                throw new \Exception('This should not run!');
            }))
            , 'Filter on None is an identity'
        );
    }

    public function test_filterNot()
    {
        $none = None();
        $this->assertTrue(
            $none === ($none->filterNot(function () {
                throw new \Exception('This should not run!');
            }))
            , 'FilterNot on None is an identity'
        );
    }

    function test_walk()
    {
        $times = 0;
        None()->walk(function () use (&$times) {
            $times += 1;
        });
        $this->assertTrue(
            0 === $times
            , 'Walk should run no times on None'
        );
    }

    function test_toArray()
    {
        $arr = None()->toArray();
        $this->assertTrue(
            is_array($arr)
            , 'The value produced by None->toArray should be an array'
        );
        $this->assertTrue(
            0 === count($arr)
            , 'The length of the array produced by None->toArray should be 0'
        );
    }

    function test_isEmpty()
    {
        $this->assertTrue(
            None()->isEmpty()
            , 'None should be empty'
        );
    }

    function test_find()
    {
        $this->assertInstanceOf(
            None::class
            , None()->find(function () {
            throw new \Exception('This should never run!');
        })
            , 'Find on none should be an identity'
        );
    }

    function test_traversable_interface()
    {
        // time to test if the interface works
        $times = 0;
        foreach (None() as $key => $value) {
            $times += 1;
        }
        $this->assertTrue(
            0 === $times
            , 'None should always be at its end'
        );
    }

    public function test_count()
    {
        $this->assertEquals(
            0
            , None()->count()
            , 'None->count should always equal 0'
        );
    }
}
