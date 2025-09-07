<?php

namespace bultonFr\Utils\Tests\Helpers;

/**
 * Methods to create a mock of a function with multiple values
 *
 * @author bulton-fr <bulton.fr@gmail.com>
 */
trait MockFunctions
{
    /**
     * Create a class which return some data and the closure to use
     * for mocked the function
     *
     * @return anonymous@class
     */
    protected function createFctMock()
    {
        return new class () {
            public $callIdx = -1;
            public $returnedValues = [];
            public $mockedFct;

            public function __construct()
            {
                $that = $this;
                $this->mockedFct = function (...$args) use ($that) {
                    $that->callIdx++;

                    if (!isset($that->returnedValues[$that->callIdx])) {
                        throw new \Exception('No value defined for this call');
                    }

                    return $that->returnedValues[$that->callIdx];
                };
            }

            public function resetIdx()
            {
                $this->callIdx = -1;
            }
        };
    }
}
