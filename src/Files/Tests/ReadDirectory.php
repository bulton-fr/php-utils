<?php

namespace bultonFr\Utils\Files\Tests\units;

use atoum;

/**
 * @engine isolate
 */
class ReadDirectory extends atoum
{
    protected $mock;
    protected $listFiles = [];
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('itemAction')
            ->makeVisible('dirAction')
            ->generate('bultonFr\Utils\Files\ReadDirectory')
        ;
        
        if ($testMethod !== 'testConstructAndGetters') {
            $this->mock = new \mock\bultonFr\Utils\Files\ReadDirectory($this->listFiles);
        }
    }
    
    public function testConstructAndGetters()
    {
        $this->assert('test Files\ReadDirectory::__construct')
            ->object($this->mock = new \mock\bultonFr\Utils\Files\ReadDirectory($this->listFiles))
                ->isInstanceOf('\bultonFr\Utils\Files\ReadDirectory')
            ->array($this->mock->getList())
                ->isIdenticalTo($this->listFiles)
            ->array($this->mock->getIgnore())
                ->isEqualTo(['.', '..'])
        ;
    }
    
    public function testRun()
    {
        $this->assert('test Files\ReadDirectory::run with opendir fail')
            ->if($this->function->opendir = false)
            ->then
            ->exception(function () {
                $this->mock->run(__DIR__);
            })
                ->hasCode(\bultonFr\Utils\Files\ReadDirectory::EXCEP_RUN_OPENDIR)
        ;
        
        $this->assert('test Files\ReadDirectory::run')
            ->given($itemActionListFiles = [])
            ->given($isDirListCheck = [])
            ->given($dirActionListPath = [])
            ->if($this->function->opendir = true)
            ->and($this->function->closedir = true)
            ->and($this->function->readdir[0] = false) //Default
            ->and($this->function->readdir[1] = '.')
            ->and($this->function->readdir[2] = '..')
            ->and($this->function->readdir[3] = 'core')
            ->and($this->function->readdir[4] = 'memcache')
            ->and($this->function->readdir[5] = 'Application.php')
            ->and($this->function->readdir[6] = 'Config.php')
            ->and($this->calling($this->mock)->itemAction = function (
                $fileName,
                $pathToFile
            ) use (&$itemActionListFiles) {
                $itemActionListFiles[] = $fileName;
                
                if ($fileName === 'Application.php') {
                    return 'break'; //So "Config.php" will not be read
                }
                
                //This if is extracted from original class
                if (in_array($fileName, ['.', '..'])) {
                    return 'continue';
                }
                
                return '';
            })
            ->and($this->function->is_dir = function ($filename) use (&$isDirListCheck) {
                $isDirListCheck[] = $filename;
                
                //"." and ".." is ignored before
                if ($filename === __DIR__.'/core') {
                    return true;
                } elseif ($filename === __DIR__.'/memcache') {
                    return true;
                }
                
                return false;
            })
            ->and($this->calling($this->mock)->dirAction = function ($dirPath) use (&$dirActionListPath) {
                $dirActionListPath[] = $dirPath;
                
                return true;
            })
            ->then
            
            ->variable($this->mock->run(__DIR__))
                ->isNull()
            ->array($itemActionListFiles)
                ->isEqualTo([
                    '.',
                    '..',
                    'core',
                    'memcache',
                    'Application.php'
                ])
                //Config.php not here because Application.php return "break"
            ->array($isDirListCheck)
                ->isEqualTo([
                    __DIR__.'/core',
                    __DIR__.'/memcache'
                ])
            ->array($dirActionListPath)
                ->isEqualTo([
                    __DIR__.'/core',
                    __DIR__.'/memcache'
                ])
        ;
    }
    
    public function testItemAction()
    {
        $this->assert('test Files\ReadDirectory::itemAction for ignored path')
            ->string($this->invoke($this->mock)->itemAction('.', __DIR__))
                ->isEqualTo('continue')
            ->string($this->invoke($this->mock)->itemAction('..', __DIR__))
                ->isEqualTo('continue')
        ;
        
        $this->assert('test Files\ReadDirectory::itemAction for not ignored path')
            ->string($this->invoke($this->mock)->itemAction('Application.php', __DIR__))
                ->isEmpty()
        ;
    }
    
    public function testDirAction()
    {
        //Not tested because we can't mock the content.
    }
}
