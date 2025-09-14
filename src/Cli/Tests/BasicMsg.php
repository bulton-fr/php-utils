<?php

namespace bultonFr\Utils\Cli\Tests\units;

use atoum;
use bultonFr\Utils\Cli\BasicMsg as BasicMsgSrc;

/**
 * @engine isolate
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BasicMsg extends atoum
{
    //Note: makeVisible() not work with protected static methods

    protected $mock;

    protected $methodCaller;

    /**
     * To avoid PHPMD warning on new mock class
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function beforeTestMethod($methodName)
    {
        $testedMethodName = lcfirst(substr($methodName, 4));

        $this->mock = new \mock\bultonFr\Utils\Cli\BasicMsg();
        $callMethod = function (...$args) use ($testedMethodName) {
            return static::{$testedMethodName}(...$args);
        };

        $this->methodCaller = $callMethod->bindTo($this->mock, $this->mock);
    }

    public function testObtainTxtColorCode()
    {
        $methodCaller = $this->methodCaller;

        $this->assert('test Cli\obtainTxtColorCode - red')
            ->integer($methodCaller('red'))
                ->isEqualTo(31)
        ;

        $this->assert('test Cli\obtainTxtColorCode - green')
            ->integer($methodCaller('green'))
                ->isEqualTo(32)
        ;

        $this->assert('test Cli\obtainTxtColorCode - yellow')
            ->integer($methodCaller('yellow'))
                ->isEqualTo(33)
        ;

        $this->assert('test Cli\obtainTxtColorCode - white')
            ->integer($methodCaller('white'))
                ->isEqualTo(37)
        ;

        $this->assert('test Cli\obtainTxtColorCode - others')
            ->integer($methodCaller('unit-test'))
                ->isEqualTo(37)
        ;
    }

    public function testObtainTxtStyleCode()
    {
        $methodCaller = $this->methodCaller;

        $this->assert('test Cli\obtainTxtStyleCode - bold')
            ->integer($methodCaller('bold'))
                ->isEqualTo(1)
        ;

        $this->assert('test Cli\obtainTxtStyleCode - normal')
            ->integer($methodCaller('normal'))
                ->isEqualTo(0)
        ;

        $this->assert('test Cli\obtainTxtStyleCode - others')
            ->integer($methodCaller('unit-test'))
                ->isEqualTo(0)
        ;
    }

    public function testFlush()
    {
        $this->assert('test Cli::flush - prepare')
            ->given($this->function->ob_flush = null)
            ->given($callFlush = function () {
                return static::flush();
            })
            ->and($callFlush = $callFlush->bindTo($this->mock, $this->mock))
        ;

        $this->assert('test Cli::flush - without buffer')
            ->if($this->function->ob_get_status = [])
            ->then
            ->variable($callFlush())
                ->isNull()
            ->function('ob_flush')
                ->never()
        ;

        $this->assert('test Cli::flush - with buffer')
            ->if($this->function->ob_get_status = ['myBuffer'])
            ->then
            ->variable($callFlush())
                ->isNull()
            ->function('ob_flush')
                ->once()
        ;
    }

    public function testDisplayMsg()
    {
        $this->assert('test Cli::displayMsg - prepare')
            ->given($this->function->ob_flush = null)
        ;

        $this->assert('test Cli::displayMsg with only a message')
            ->output(function () {
                BasicMsgSrc::displayMsg('hi from unit-test !');
            })
                ->isEqualTo('hi from unit-test !')
        ;

        $this->assert('test Cli::displayMsg with a color')
            ->output(function () {
                BasicMsgSrc::displayMsg('hi from unit-test !', 'yellow');
            })
                ->isEqualTo("\033[0;33mhi from unit-test !\033[0m")
        ;

        $this->assert('test Cli::displayMsg with a color and style')
            ->output(function () {
                BasicMsgSrc::displayMsg('hi from unit-test !', 'green', 'bold');
            })
                ->isEqualTo("\033[1;32mhi from unit-test !\033[0m")
        ;
    }

    public function testDisplayMsgNL()
    {
        $this->assert('test Cli::displayMsgNL - prepare')
            ->given($this->function->ob_flush = null)
        ;

        $this->assert('test Cli::displayMsgNL with only a message')
            ->output(function () {
                BasicMsgSrc::displayMsgNL('hi from unit-test !');
            })
                ->isEqualTo('hi from unit-test !' . "\n")
            /*
             * Does not seem to see static method :/
             * Method 'mock\bultonFr\Utils\Cli\BasicMsg::displayMsg()' does not exist
             *
            ->mock($this->mock)
                ->call('displayMsg')
                    ->withArguments('hi from unit-test !')
                        ->once()
             */
        ;

        $this->assert('test Cli::displayMsgNL with a color')
            ->output(function () {
                BasicMsgSrc::displayMsgNL('hi from unit-test !', 'yellow');
            })
                ->isEqualTo("\033[0;33mhi from unit-test !\033[0m\n")
        ;

        $this->assert('test Cli::displayMsgNL with a color and style')
            ->output(function () {
                BasicMsgSrc::displayMsgNL('hi from unit-test !', 'green', 'bold');
            })
                ->isEqualTo("\033[1;32mhi from unit-test !\033[0m\n")
        ;
    }
}
