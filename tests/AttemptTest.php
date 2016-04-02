<?php
namespace Phpixme;

class AttemptTest extends PhpixmeTestCase
{

    public function test_companion_returns_children() {
        $this->assertInstanceOf(
            Success::class
            , Attempt(function(){})
            , "No thrown values produce a Success"
        );
        $this->assertInstanceOf(
            Failure::class
            , Attempt(function (){ throw new \Exception(); })
            , 'Throwing an exception produces a Failure'
        );
    }
}
