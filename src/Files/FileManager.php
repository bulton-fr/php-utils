<?php

declare(strict_types=1);

namespace bultonFr\Utils\Files;

use Exception;

/**
 * Some methods to manage files and folders
 *
 * @author bulton-fr <bulton.fr@gmail.com>
 */
class FileManager
{
    /**
     * Exception code when the constructor's first parameter is not null and
     * is not an instance of \Psr\Log\LoggerInterface
     */
    public const EXCEP_NOT_LOGGER_INTERFACE = 202001;

    /**
     * Exception code when a file already exist
     *
     * @const EXCEP_FILE_EXIST
     */
    public const EXCEP_FILE_EXIST = 202002;

    /**
     * Exception code when a file not exist
     *
     * @const EXCEP_FILE_NOT_EXIST
     */
    public const EXCEP_FILE_NOT_EXIST = 202003;

    /**
     * Exception code when the symlink target not exist
     *
     * @const EXCEP_LINK_TARGET_NOT_FOUND
     */
    public const EXCEP_LINK_TARGET_NOT_FOUND = 202004;

    /**
     * Exception code if the creating of the symlink failed
     *
     * @const EXCEP_LINK_CREATION_FAILED
     */
    public const EXCEP_LINK_CREATION_FAILED = 202005;

    /**
     * Exception code when the deleting of the symlink failed
     *
     * @const EXCEP_LINK_REMOVE_FAILED
     */
    public const EXCEP_LINK_REMOVE_FAILED = 202006;

    /**
     * Exception code when a directory already exist
     *
     * @const EXCEP_DIRECTORY_EXIST
     */
    public const EXCEP_DIRECTORY_EXIST = 202007;

    /**
     * Exception code when the directory creation failed
     *
     * @const EXCEP_DIRECTORY_CREATION_FAILED
     */
    public const EXCEP_DIRECTORY_CREATION_FAILED = 202008;

    /**
     * Exception code when the file to copy not exist
     *
     * @const EXCEP_COPY_SOURCE_NOT_FOUND
     */
    public const EXCEP_COPY_SOURCE_NOT_FOUND = 202009;

    /**
     * Exception code when a copy failed
     *
     * @const EXCEP_COPY_FAILED
     */
    public const EXCEP_COPY_FAILED = 202010;

    /**
     * Exception code when a directory deletion failed
     *
     * @const EXCEP_DIRECTORY_REMOVE_FAIL
     */
    public const EXCEP_DIRECTORY_REMOVE_FAIL = 202011;

    /**
     * The Logger instance where all debug message will be sent
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * The Logger level to use to send messages
     *
     * @var string
     */
    protected $loggerMsgType = '';

    /**
     * Constructor
     *
     * @param mixed $logger The Logger instance
     * where all debug message will be sent
     * @param string $loggerMsgType (default 'debug') The Logger level to use
     * to send messages
     */
    public function __construct($logger = null, string $loggerMsgType = 'debug')
    {
        if ($logger !== null && !($logger instanceof \Psr\Log\LoggerInterface)) {
            throw new Exception(
                'The constructor first parameter must be an instance of \Psr\Log\LoggerInterface.',
                static::EXCEP_NOT_LOGGER_INTERFACE
            );
        }

        $this->logger        = $logger;
        $this->loggerMsgType = $loggerMsgType;
    }

    /**
     * Obtain the Paths class
     * Like that, allow override Path class
     *
     * @return string
     */
    public function obtainPaths(): string
    {
        return Paths::class;
    }

    /**
     * Get the value of logger
     *
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get the value of loggerMsgType
     *
     * @return string
     */
    public function getLoggerMsgType(): string
    {
        return $this->loggerMsgType;
    }

    /**
     * Send a message to logger
     *
     * @param array ...$args Arguments passed to Logger function
     * @return void
     */
    protected function sendMsgInLogger(...$args)
    {
        if ($this->logger === null) {
            return;
        }

        $msgType = $this->loggerMsgType;
        $this->logger->{$msgType}(...$args);
    }

    /**
     * Create a symlink
     *
     * @param string $linkTarget The symlink target path
     * @param string $linkFile The symlink file path
     * @param bool $tryRelative (default true) If system try to resolv paths
     *  to use a relative path for target.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function createSymLink(
        string $linkTarget,
        string $linkFile,
        bool $tryRelative = true
    ) {
        $this->sendMsgInLogger(
            'FileManager - Create symlink',
            [
                'linkTarget' => $linkTarget,
                'linkFile'   => $linkFile
            ]
        );

        if (file_exists($linkFile)) {
            throw new Exception(
                'link file ' . $linkFile . ' already exist.',
                static::EXCEP_FILE_EXIST
            );
        }

        if (file_exists($linkTarget) === false) {
            throw new Exception(
                'link target ' . $linkTarget . ' not found.',
                static::EXCEP_LINK_TARGET_NOT_FOUND
            );
        }

        $usedTarget = $linkTarget;
        if ($tryRelative === true) {
            try {
                $pathsClass = $this->obtainPaths();
                $usedTarget = $pathsClass::absoluteToRelative($linkTarget, $linkFile);
                $this->sendMsgInLogger(
                    'FileManager - Create symlink - Use relative path',
                    ['target' => $usedTarget]
                );
            } catch (Exception $e) {
                $usedTarget = $linkTarget;
            }
        }

        $status = symlink($usedTarget, $linkFile);
        if ($status === false) {
            throw new Exception(
                'link create failed for ' . $linkFile . ' -> ' . $usedTarget,
                static::EXCEP_LINK_CREATION_FAILED
            );
        }
    }

    /**
     * Remove a symlink
     *
     * @param string $linkFile The symlink file path
     *
     * @return void
     */
    public function removeSymLink(string $linkFile)
    {
        $this->sendMsgInLogger(
            'FileManager - Remove symlink',
            ['linkFile' => $linkFile]
        );

        if (file_exists($linkFile) === false) {
            throw new Exception(
                'link file ' . $linkFile . ' not found.',
                static::EXCEP_FILE_NOT_EXIST
            );
        }

        $status = unlink($linkFile);
        if ($status === false) {
            throw new Exception(
                'link remove failed for ' . $linkFile,
                static::EXCEP_LINK_REMOVE_FAILED
            );
        }
    }

    /**
     * Create a new directory
     *
     * @param string $dirPath The directory path
     *
     * @return void
     */
    public function createDirectory(string $dirPath)
    {
        $this->sendMsgInLogger(
            'FileManager - Create directory',
            ['path' => $dirPath]
        );

        if (file_exists($dirPath) === true) {
            throw new Exception(
                'Directory ' . $dirPath . ' already exist.',
                static::EXCEP_DIRECTORY_EXIST
            );
        }

        $status = mkdir($dirPath, 0755);
        if ($status === false) {
            throw new Exception(
                'Directory ' . $dirPath . ' creation failed.',
                static::EXCEP_DIRECTORY_CREATION_FAILED
            );
        }
    }

    /**
     * Copy a file
     *
     * @param string $source The source file path
     * @param string $target The destination file path
     *
     * @return void
     */
    public function copyFile(string $source, string $target)
    {
        $this->sendMsgInLogger(
            'FileManager - Copy file',
            [
                'source' => $source,
                'target' => $target
            ]
        );

        if (file_exists($target)) {
            throw new Exception(
                'target file ' . $target . ' already exist.',
                static::EXCEP_FILE_EXIST
            );
        }

        if (file_exists($source) === false) {
            throw new Exception(
                'copy source ' . $source . ' not found.',
                static::EXCEP_COPY_SOURCE_NOT_FOUND
            );
        }

        $status = copy($source, $target);
        if ($status === false) {
            throw new Exception(
                'copy failed for ' . $source . ' -> ' . $target,
                static::EXCEP_COPY_FAILED
            );
        }
    }

    /**
     * Remove folders recursively
     *
     * @see http://php.net/manual/fr/function.rmdir.php#110489
     *
     * @param string $dirPath Path to directory to remove
     *
     * @return void
     */
    public function removeRecursiveDirectory(string $dirPath)
    {
        $this->sendMsgInLogger(
            'FileManager - Remove files and directories',
            ['path' => $dirPath]
        );

        $itemList = array_diff(scandir($dirPath), ['.', '..']);

        foreach ($itemList as $itemName) {
            $itemPath = $dirPath . '/' . $itemName;

            if (is_dir($itemPath)) {
                $this->removeRecursiveDirectory($itemPath);
                continue;
            }

            unlink($itemPath);
        }

        $rmDirStatus = rmdir($dirPath);
        if ($rmDirStatus === false) {
            throw new Exception(
                'Directory deletion has failed for ' . $dirPath,
                static::EXCEP_DIRECTORY_REMOVE_FAIL
            );
        }
    }
}
