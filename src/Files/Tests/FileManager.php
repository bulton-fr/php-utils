<?php

namespace bultonFr\Utils\Files\Tests\units;

use atoum;
use bultonFr\Utils\Tests\Helpers\MockFunctions;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

/**
 * @engine isolate
 */
class FileManager extends atoum
{
    use MockFunctions;

    protected $mock;

    protected $logger;

    protected $handler;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('sendMsgInLogger')
            ->generate('bultonFr\Utils\Files\FileManager')
        ;

        $this->logger  = new Logger('FileManager-unit-test');
        $this->handler = new TestHandler;
        $this->logger->pushHandler($this->handler);
        
        if ($testMethod === 'testConstructAndGetters') {
            return;
        }

        $this->mock = new \mock\bultonFr\Utils\Files\FileManager($this->logger);
    }

    public function testConstructAndGetters()
    {
        $this->assert('test Files\FileManager::__construct without logger')
            ->object($this->mock = new \mock\bultonFr\Utils\Files\FileManager)
                ->isInstanceOf('\bultonFr\Utils\Files\FileManager')
            ->variable($this->mock->getLogger())
                ->isNull()
            ->string($this->mock->getLoggerMsgType())
                ->isEqualTo('debug')
        ;

        $this->assert('test Files\FileManager::__construct with a logger')
            ->object($this->mock = new \mock\bultonFr\Utils\Files\FileManager($this->logger, 'info'))
                ->isInstanceOf('\bultonFr\Utils\Files\FileManager')
            ->object($this->mock->getLogger())
                ->isInstanceOf($this->logger)
            ->string($this->mock->getLoggerMsgType())
                ->isEqualTo('info')
        ;

        $this->assert('test Files\FileManager::__construct with a logger')
            ->exception(function () {
                $this->mock = new \mock\bultonFr\Utils\Files\FileManager(new \stdClass);
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_NOT_LOGGER_INTERFACE)
        ;
    }

    public function testSendMsgInLogger()
    {
        $this->assert('test Files\FileManager::sendMsgInLogger - prepare')
            ->given($setLoggerMsgType = function (string $loggerMsgType) {
                $this->loggerMsgType = $loggerMsgType;
            })
            ->and($setLoggerMsgType = $setLoggerMsgType->bindTo($this->mock, $this->mock))
            ->then
            ->given($setLogger = function ($logger) {
                $this->logger = $logger;
            })
            ->and($setLogger = $setLogger->bindTo($this->mock, $this->mock))
        ;

        $this->assert('test Files\FileManager::sendMsgInLogger with logger and default msgType')
            ->variable($this->mock->sendMsgInLogger('unit test - case 1'))
                ->isNull()
            ->boolean($this->handler->hasDebug('unit test - case 1'))
                ->isTrue()
        ;

        $this->assert('test Files\FileManager::sendMsgInLogger with logger and defined msgType')
            ->if($setLoggerMsgType('info'))
            ->variable($this->mock->sendMsgInLogger('unit test - case 2'))
                ->isNull()
            ->boolean($this->handler->hasDebug('unit test - case 2'))
                ->isFalse()
            ->boolean($this->handler->hasInfo('unit test - case 2'))
                ->isTrue()
        ;

        $this->assert('test Files\FileManager::sendMsgInLogger without logger')
            ->if($setLogger(null))
            ->then
            ->variable($this->mock->sendMsgInLogger('unit test - case 3'))
                ->isNull()
            ->boolean($this->handler->hasDebug('unit test - case 3'))
                ->isFalse()
            ->boolean($this->handler->hasInfo('unit test - case 3'))
                ->isFalse()
        ;
    }

    public function testCreateSymlink()
    {
        $this->assert('test Files\FileManager::createSymlink - prepare')
            ->given($fileExistsMock = $this->createFctMock())
            ->and($this->function->file_exists = $fileExistsMock->mockedFct)
        ;

        $this->assert('test Files\FileManager::createSymlink - creation success')
            ->if($fileExistsMock->returnedValues = [false, true])
            ->and($this->function->symlink = true)
            ->then
            ->variable($this->mock->createSymlink('target/file', 'dest/file'))
                ->isNull()
            ->function('file_exists')
                ->wasCalledWithArguments('target/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('symlink')
                ->wasCalledWithArguments('target/file', 'dest/file')
                    ->once()
            ->boolean($this->handler->hasDebug('FileManager - Create symlink'))
                ->isTrue()
            ->array($records = $this->handler->getRecords())
            ->array($context = reset($records)['context'])
                ->isEqualTo([
                    'linkTarget' => 'target/file',
                    'linkFile'   => 'dest/file'
                ])
        ;

        $this->assert('test Files\FileManager::createSymlink - creation failed - link file exist')
            ->if($fileExistsMock->returnedValues = [true, true])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->symlink = true)
            ->then
            ->exception(function () {
                $this->mock->createSymlink('target/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_FILE_EXIST)
            ->function('file_exists')
                ->wasCalledWithArguments('target/file')
                    ->never()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('symlink')
                ->wasCalledWithArguments('target/file', 'dest/file')
                    ->never()
        ;

        $this->assert('test Files\FileManager::createSymlink - creation failed - target file not exist')
            ->if($fileExistsMock->returnedValues = [false, false])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->symlink = true)
            ->then
            ->exception(function () {
                $this->mock->createSymlink('target/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_LINK_TARGET_NOT_FOUND)
            ->function('file_exists')
                ->wasCalledWithArguments('target/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('symlink')
                ->wasCalledWithArguments('target/file', 'dest/file')
                    ->never()
        ;

        $this->assert('test Files\FileManager::createSymlink - creation failed - symlink call failed')
            ->if($fileExistsMock->returnedValues = [false, true])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->symlink = false)
            ->then
            ->exception(function () {
                $this->mock->createSymlink('target/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_LINK_CREATION_FAILED)
            ->function('file_exists')
                ->wasCalledWithArguments('target/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('symlink')
                ->wasCalledWithArguments('target/file', 'dest/file')
                    ->once()
        ;
    }

    public function testRemoveSymlink()
    {
        $this->assert('test Files\FileManager::removeSymlink - remove success')
            ->if($this->function->file_exists = true)
            ->and($this->function->unlink = true)
            ->then
            ->variable($this->mock->removeSymlink('dest/file'))
                ->isNull()
            ->function('file_exists')
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('unlink')
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->boolean($this->handler->hasDebug('FileManager - Remove symlink'))
                ->isTrue()
            ->array($records = $this->handler->getRecords())
            ->array($context = reset($records)['context'])
                ->isEqualTo(['linkFile' => 'dest/file'])
        ;

        $this->assert('test Files\FileManager::removeSymlink - remove failed - link file not exist')
            ->if($this->function->file_exists = false)
            ->and($this->function->unlink = true)
            ->then
            ->exception(function () {
                $this->mock->removeSymlink('dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_FILE_NOT_EXIST)
            ->function('file_exists')
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('unlink')
                ->wasCalledWithArguments('dest/file')
                    ->never()
        ;

        $this->assert('test Files\FileManager::removeSymlink - remove failed - unlink call failed')
            ->if($this->function->file_exists = true)
            ->and($this->function->unlink = false)
            ->then
            ->exception(function () {
                $this->mock->removeSymlink('dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_LINK_REMOVE_FAILED)
            ->function('file_exists')
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('unlink')
                ->wasCalledWithArguments('dest/file')
                    ->once()
        ;
    }

    public function testCreateDirectory()
    {
        $this->assert('test Files\FileManager::createDirectory - creation success')
            ->if($this->function->file_exists = false)
            ->and($this->function->mkdir = true)
            ->then
            ->variable($this->mock->createDirectory('dest/dir'))
                ->isNull()
            ->function('file_exists')
                ->wasCalledWithArguments('dest/dir')
                    ->once()
            ->function('mkdir')
                ->wasCalledWithArguments('dest/dir', 0755)
                    ->once()
            ->boolean($this->handler->hasDebug('FileManager - Create directory'))
                ->isTrue()
            ->array($records = $this->handler->getRecords())
            ->array($context = reset($records)['context'])
                ->isEqualTo(['path' => 'dest/dir'])
        ;

        $this->assert('test Files\FileManager::createDirectory - creation failed - directory already exist')
            ->if($this->function->file_exists = true)
            ->and($this->function->mkdir = true)
            ->then
            ->exception(function () {
                $this->mock->createDirectory('dest/dir');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_DIRECTORY_EXIST)
            ->function('file_exists')
                ->wasCalledWithArguments('dest/dir')
                    ->once()
            ->function('mkdir')
                ->wasCalledWithArguments('dest/dir', 0755)
                    ->never()
        ;

        $this->assert('test Files\FileManager::createDirectory - creation failed - mkdir call failed')
        ->if($this->function->file_exists = false)
        ->and($this->function->mkdir = false)
        ->then
        ->exception(function () {
            $this->mock->createDirectory('dest/dir');
        })
            ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_DIRECTORY_CREATION_FAILED)
        ->function('file_exists')
            ->wasCalledWithArguments('dest/dir')
                ->once()
        ->function('mkdir')
            ->wasCalledWithArguments('dest/dir', 0755)
                ->once()
        ;
    }

    public function testCopyFile()
    {
        $this->assert('test Files\FileManager::copyFile - prepare')
            ->given($fileExistsMock = $this->createFctMock())
            ->and($this->function->file_exists = $fileExistsMock->mockedFct)
        ;

        $this->assert('test Files\FileManager::copyFile - copy success')
            ->if($fileExistsMock->returnedValues = [false, true])
            ->and($this->function->copy = true)
            ->then
            ->variable($this->mock->copyFile('source/file', 'dest/file'))
                ->isNull()
            ->function('file_exists')
                ->wasCalledWithArguments('source/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('copy')
                ->wasCalledWithArguments('source/file', 'dest/file')
                    ->once()
            ->boolean($this->handler->hasDebug('FileManager - Copy file'))
                ->isTrue()
            ->array($records = $this->handler->getRecords())
            ->array($context = reset($records)['context'])
                ->isEqualTo([
                    'source' => 'source/file',
                    'target' => 'dest/file'
                ])
        ;

        $this->assert('test Files\FileManager::createSymlink - copy failed - dest file exist')
            ->if($fileExistsMock->returnedValues = [true, true])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->copy = true)
            ->then
            ->exception(function () {
                $this->mock->copyFile('source/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_FILE_EXIST)
            ->function('file_exists')
                ->wasCalledWithArguments('source/file')
                    ->never()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('copy')
                ->wasCalledWithArguments('source/file', 'dest/file')
                    ->never()
        ;

        $this->assert('test Files\FileManager::createSymlink - copy failed - source file not exist')
            ->if($fileExistsMock->returnedValues = [false, false])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->copy = true)
            ->then
            ->exception(function () {
                $this->mock->copyFile('source/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_COPY_SOURCE_NOT_FOUND)
            ->function('file_exists')
                ->wasCalledWithArguments('source/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('copy')
                ->wasCalledWithArguments('source/file', 'dest/file')
                    ->never()
        ;

        $this->assert('test Files\FileManager::createSymlink - copy failed - copy call failed')
            ->if($fileExistsMock->returnedValues = [false, true])
            ->and($fileExistsMock->resetIdx())
            ->and($this->function->copy = false)
            ->then
            ->exception(function () {
                $this->mock->copyFile('source/file', 'dest/file');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_COPY_FAILED)
            ->function('file_exists')
                ->wasCalledWithArguments('source/file')
                    ->once()
                ->wasCalledWithArguments('dest/file')
                    ->once()
            ->function('copy')
                ->wasCalledWithArguments('source/file', 'dest/file')
                    ->once()
        ;
    }
    
    public function testRemoveRecursiveDirectory()
    {
        $this->assert('test Files\FileManager::removeRecursiveDirectory without file')
            ->if($this->function->scandir = ['.', '..'])
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->variable($this->mock->removeRecursiveDirectory('unit-test-dir'))
                ->isNull()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->once()
            ->function('unlink')
                ->never()
        ;
        
        $this->assert('test Files\FileManager::removeRecursiveDirectory with only file')
            ->if($this->function->scandir = ['.', '..', 'test.php', 'src.php'])
            ->and($this->function->is_dir = false)
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->variable($this->mock->removeRecursiveDirectory('unit-test-dir'))
                ->isNull()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->withArguments('unit-test-dir')
                        ->once()
            ->function('unlink')
                ->wasCalled()
                    ->twice()
                ->wasCalledWithArguments('unit-test-dir/test.php')
                    ->once()
                ->wasCalledWithArguments('unit-test-dir/src.php')
                    ->once()
        ;
        
        $this->assert('test Files\FileManager::removeRecursiveDirectory with file and directory')
            ->if($this->function->scandir = function ($path) {
                if ($path === 'unit-test-dir') {
                    return ['.', '..', 'test.php', 'src', 'config.php'];
                }
                
                return ['.', '..'];
            })
            ->and($this->function->is_dir = function ($path) {
                if ($path === 'unit-test-dir/src') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->variable($this->mock->removeRecursiveDirectory('unit-test-dir'))
                ->isNull()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->withArguments('unit-test-dir/src')
                        ->once()
                    ->withArguments('unit-test-dir')
                        ->once()
            ->function('unlink')
                ->wasCalled()
                    ->twice()
                ->wasCalledWithArguments('unit-test-dir/test.php')
                    ->once()
                ->wasCalledWithArguments('unit-test-dir/config.php')
                    ->once()
            ->function('rmdir')
                ->wasCalled()
                    ->twice()
                ->wasCalledWithArguments('unit-test-dir/src')
                    ->once()
                ->wasCalledWithArguments('unit-test-dir')
                    ->once()
        ;

        $this->assert('test Files\FileManager::removeRecursiveDirectory with rmdir failed')
            ->if($this->function->scandir = ['.', '..'])
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = false)
            ->then
            
            ->exception(function () {
                $this->mock->removeRecursiveDirectory('unit-test-dir');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_DIRECTORY_REMOVE_FAIL)
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->once()
            ->function('unlink')
                ->never()
        ;
        
        $this->assert('test Files\FileManager::removeRecursiveDirectory with rmdir failed on sub-directory')
            ->if($this->function->scandir = function ($path) {
                if ($path === 'unit-test-dir') {
                    return ['.', '..', 'test.php', 'src', 'config.php'];
                }
                
                return ['.', '..'];
            })
            ->and($this->function->is_dir = function ($path) {
                if ($path === 'unit-test-dir/src') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = function ($path) {
                if ($path === 'unit-test-dir/src') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            ->exception(function () {
                $this->mock->removeRecursiveDirectory('unit-test-dir');
            })
                ->hasCode(\bultonFr\Utils\Files\FileManager::EXCEP_DIRECTORY_REMOVE_FAIL)
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->withArguments('unit-test-dir/src')
                        ->once()
                    ->withArguments('unit-test-dir')
                        ->once()
            ->function('unlink')
                ->wasCalled()
                    ->once()
                ->wasCalledWithArguments('unit-test-dir/test.php')
                    ->once()
                ->wasCalledWithArguments('unit-test-dir/config.php')
                    ->never()
            ->function('rmdir')
                ->wasCalled()
                    ->once()
                ->wasCalledWithArguments('unit-test-dir/src')
                    ->once()
                ->wasCalledWithArguments('unit-test-dir')
                    ->never()
        ;
    }
}
