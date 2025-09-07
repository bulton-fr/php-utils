<?php

declare(strict_types=1);

namespace bultonFr\Utils\Files;

use Exception;

/**
 * Read a directory and sub-directory, and do the choice action on each
 * item in the readed directory.
 *
 * @author bulton-fr <bulton.fr@gmail.com>
 */
class ReadDirectory
{
    /**
     * Exception code if the opendir function fail
     *
     * @const EXCEP_RUN_OPENDIR
     */
    public const EXCEP_RUN_OPENDIR = 201001;

    /**
     * A list of path. The system not add all path found automaticaly, you need
     * to add it in a override of itemAction method.
     *
     * @var array $list
     */
    protected $list;

    /**
     * Item to ignore during the reading of directories
     *
     * @var array $ignore
     */
    protected $ignore = ['.', '..'];

    /**
     * Constructor
     *
     * @param array &$listFiles : List of file(s) found
     */
    public function __construct(array &$listFiles)
    {
        $this->list = &$listFiles;
    }

    /**
     * Getter accessor to the property list
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Getter accessor to the property ignore
     *
     * @return array
     */
    public function getIgnore(): array
    {
        return $this->ignore;
    }

    /**
     * Read all the directories
     *
     * @param string $path : Path to read
     *
     * @return void
     */
    public function run(string $path)
    {
        $dir = opendir($path);
        if ($dir === false) {
            throw new Exception(
                'The directory can not be open. '
                . 'See php error log for more informations.',
                self::EXCEP_RUN_OPENDIR
            );
        }

        while (($file = readdir($dir)) !== false) {
            $action = $this->itemAction($file, $path);

            if ($action === 'continue') {
                continue;
            } elseif ($action === 'break') {
                break;
            }

            if (is_dir($path . '/' . $file)) {
                $this->dirAction($path . '/' . $file);
                continue;
            }
        }

        closedir($dir);
    }

    /**
     * Action to do when an item (file or directory) is found.
     *
     * @param string $fileName The file's name
     * @param string $pathToFile The file's path (unused in base implementation)
     *
     * @return string
     */
    protected function itemAction(string $fileName, string $pathToFile): string
    {
        if (in_array($fileName, $this->ignore)) {
            return 'continue';
        }

        return '';
    }

    /**
     * Recall ReadDirectory to read this directory
     * This is to avoid having the recursion error
     *
     * @param string $dirPath
     *
     * @return void
     */
    protected function dirAction(string $dirPath)
    {
        $read = new self($this->list);
        $read->run($dirPath);
    }
}
