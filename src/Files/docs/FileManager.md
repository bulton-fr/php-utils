# Utils\Files\FileManager

This class give you many methods to manage files and directories.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`EXCEP_NOT_LOGGER_INTERFACE`__
Exception code when the constructor's first parameter is not null and is not an instance of `\Psr\Log\LoggerInterface`.

__`EXCEP_FILE_EXIST`__
Exception code when a file already exist.

__`EXCEP_FILE_NOT_EXIST`__
Exception code when a file not exist.

__`EXCEP_LINK_TARGET_NOT_FOUND`__
Exception code when the symlink target not exist.

__`EXCEP_LINK_CREATION_FAILED`__
Exception code if the creating of the symlink failed.

__`EXCEP_LINK_REMOVE_FAILED`__
Exception code when the deleting of the symlink failed.

__`EXCEP_DIRECTORY_EXIST`__
Exception code when a directory already exist.

__`EXCEP_DIRECTORY_CREATION_FAILED`__
Exception code when the directory creation failed.

__`EXCEP_COPY_SOURCE_NOT_FOUND`__
Exception code when the file to copy not exist.

__`EXCEP_COPY_FAILED`__
Exception code when a copy failed.

__`EXCEP_DIRECTORY_REMOVE_FAIL`__
Exception code when a directory deletion failed.

## Properties

__`protected \Psr\Log\LoggerInterface|null $logger;`__
The Logger instance where all debug message will be sent.

__`protected string $loggerMsgType;`__
The Logger level to use to send messages.

## Methods

__`self public __construct(\Psr\Log\LoggerInterface|null $logger, string $loggerMsgType = 'debug')`__

The argument `$logger` can be passed to have some debug infos sent to a log system (like Monolog) when a method which interact with file or directories is called.  
And the argument `$loggerMsgType` is the message level sent to the log. By default the value is `debug`.

__`void protected sendMsgInLogger(...$args)`__

It's the method called by others methods to sent a message in Logger.  
If no logger is defined, nothing is happening.  
If a logger is defined, the method for declared level will be called and `$args` will be passed to this method (like you have called the method yourself, without any intermediary).

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`\Psr\Log\LoggerInterface|null public getLogger()`__

__`string public getLoggerMsgType()`__

### Symlink

__`void public function createSymLink(string $linkTarget, string $linkFile)`__

To create the new symlink at `$linkFile` which will be target `$linkTarget`.

Many exception can be thrown, the list of constants used for the code :

* `EXCEP_FILE_EXIST` : If the symlink file already exist
* `EXCEP_LINK_TARGET_NOT_FOUND` : If the target file not exist
* `EXCEP_LINK_CREATION_FAILED` : If the symlink creation failed; It's when the php function [symlink](https://www.php.net/manual/en/function.symlink.php) return `false`.

__`void public function removeSymLink(string $linkFile)`__

To remove the symlink file at `$linkFile`.

Many exception can be thrown, the list of constants used for the code :

* `EXCEP_FILE_NOT_EXIST` : If the symlink file not exist
* `EXCEP_LINK_REMOVE_FAILED` : If the symlink deletion failed; It's when the php function [unlink](https://www.php.net/manual/en/function.unlink.php) return `false`.

### Copy

__`void public function copyFile(string $source, string $target)`__

Copy the file at `$source` to `$target`.

Many exception can be thrown, the list of constants used for the code :

* `EXCEP_FILE_EXIST` : If the destination file already exist
* `EXCEP_COPY_SOURCE_NOT_FOUND` : If the source file not exist
* `EXCEP_COPY_FAILED` : If the copy failed; It's when the php function [copy](https://www.php.net/manual/en/function.copy.php) return `false`.

### Directories

__`void public function createDirectory(string $dirPath)`__

Create a new directory at `$dirPath`.  
The directory will be create with permission `0755` (2nd argument of `mkdir` function).

Many exception can be thrown, the list of constants used for the code :

* `EXCEP_DIRECTORY_EXIST` : If the directory already exist
* `EXCEP_DIRECTORY_CREATION_FAILED` : If the directory creation failed; It's when the php function [mkdir](https://www.php.net/manual/en/function.mkdir.php) return `false`.

__`void public function removeRecursiveDirectory(string $dirPath)`__

Remove the directory at `$dirPath` and all subdirectory (and files) into it.

Note: originally this method was created to remove a specific folder which not contains too many level of sub-directories. So the current implementation consider that there will be little recursion in it, so that explain why it's a simple recall of the method.
