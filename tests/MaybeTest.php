<?php

namespace Phpixme;


class MaybeTest extends PhpixmeTestCase
{

    public function test_maybe_companion() {
        $this->assertStringEndsWith(
            '\Maybe'
            , Maybe::class
            , 'Ensure the constant ends with the function name'
        );
    }
    public function maybeEmptyProvider()
    {
        return [
            [[]]
            , [null]
        ];
    }

    public function maybeSomethingProvider()
    {
        return [
            [0]
            , ['']
            , [None()]
            , [false]
            , [true]
            , [1]
            , [1.1]
            , ['1']
            , [[1]]
            , [new \stdClass()]
            , [Some('')]
        ];
    }


    /**
     * @dataProvider maybeEmptyProvider
     */
    public function test_Maybe_companion_none_result($value)
    {

        $this->assertInstanceOf(
            None::class
            , Maybe($value)
            , 'Value ' . json_encode($value, true) . ' should be of type None'
        );
    }

    /**
     * @dataProvider maybeSomethingProvider
     */
    public function test_Maybe_companion_some_result($value)
    {
        $this->assertInstanceOf(
            Some::class 
            , Maybe($value)
            , 'Value ' . json_encode($value, true) . ' should be of type Some'
        );
    }

    /**
     * @dataProvider maybeEmptyProvider
     */
    public function test_static_of_nothing($value)
    {
        $this->assertInstanceOf(
            None::class
            , Maybe::of($value)
            , 'Value ' . json_encode($value, true) . ' should be of type Null'
        );
    }

    /**
     * @dataProvider maybeSomethingProvider
     */
    public function test_static_of_something($value)
    {

        $this->assertInstanceOf(
            Some::class
            , Maybe::of($value)
            , 'Value ' . json_encode($value, true) . ' should be of type Some'
        );
    }

    /**
     * @dataProvider maybeEmptyProvider
     */
    public function test_static_from_nothing($value)
    {

        $this->assertInstanceOf(
            None::class
            , Maybe::from($value)
            , 'Value ' . json_encode($value, true) . ' should be of type Null'
        );

    }

    /**
     * @dataProvider maybeSomethingProvider
     */
    public function test_static_from_something($value)
    {
        $this->assertInstanceOf(
            Some::class
            , Maybe::from($value)
            , 'Value ' . json_encode($value, true) . ' should be of type Some'
        );
    }
}
