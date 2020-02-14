<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\AsyncDispatch;

use AsyncDispatch\Instance;

class Directory
{
    use Instance;
    public $dirPath     = null;
    public $handle      = null;
    public $lastMessage = null;

    public function getDirPath()
    {
        return $this->dirPath;
    }

    public function setDirPath($dirPath)
    {
        $this->dirPath = $dirPath;
        return $this;
    }

    protected function __construct($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    public function open($force = true)
    {
        try {
            if ($force && !is_dir($this->dirPath)) {
                mkdir($this->dirPath, 0777, true);
            }
            $this->lastMessage = $this->handle = opendir($this->dirPath);
        } catch (\Exception|\Error $exception) {
            debug($exception->getMessage());
        }
        if (false === $this->handle) {
            throw new \Exception(sprintf("open dir:%s failed!", $this->dirPath));
        }
        return $this;
    }

    public function read()
    {
        return readdir($this->handle);
    }

    public function getGenerator($hiddenNormalDir = true, $prefix = true)
    {
        while (false !== ($dirName = $this->read())) {
            if (($dirName == '.' || $dirName == '..') && $hiddenNormalDir) {
                continue;
            }
            yield $prefix ? $this->dirPath . '/' . $dirName : $dirName;
        }
    }

    public function scan(bool $recursion = false): ?array
    {
        return $this->scanDo($this->getDirPath(), $recursion);
    }

    protected function scanDo(string $scanDir, bool $recursion = false): ?array
    {
        if (null == $scanDir) {
            return null;
        }
        $scanDir  = str_replace("\\", DIRECTORY_SEPARATOR, $scanDir);
        $fileInfo = scandir($scanDir);
        if (false === $fileInfo) {
            return null;
        }
        $fileList = array_diff($fileInfo, ['.', '..']);
        if (empty($fileList)) {
            return null;
        }
        $fileArray = [];
        foreach ($fileList as $key => $file) {
            $currentFile = $scanDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($currentFile)) {
                $fileArray[$key] = File::getInstance($currentFile);
            } elseif (is_dir($currentFile) && true == $recursion) {
                $fileArray[$key] = $this->scanDo($currentFile);
            }
        }
        return $fileArray;
    }

    public function closedir()
    {
        return $this->lastMessage = closedir($this->handle);
    }

    public function lock($operation)
    {
        return $this->lastMessage = flock($this->handle, $operation);
    }

    public function cwd()
    {
        return getcwd();
    }
}
