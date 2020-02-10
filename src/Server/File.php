<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server;

class File
{
    public           $filename    = "";
    public           $handle      = null;
    protected static $instance    = [];
    public           $lastMessage = null;

    protected function __construct($filename)
    {
        $this->setFilename($filename);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): File
    {
        $this->filename = $filename;
        return $this;
    }

    public static function getInstance($filePath):self
    {
        if (empty(self::$instance[$filePath])) {
            self::$instance[$filePath] = new self($filePath);
        }
        return self::$instance[$filePath];
    }

    public function open($mode)
    {
        if (empty($this->handle)) {
            $this->handle = $this->lastMessage = fopen($this->getFilename(), $mode);
        }
        return $this;
    }

    public function content()
    {
        return file_get_contents($this->getFilename());
    }

    public function exists()
    {
        return file_exists($this->getFilename());
    }

    public function write($string)
    {
        $this->lastMessage = fwrite($this->handle, $string);
        return $this;
    }

    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    public function touch($time = null, $atime = null)
    {
        $this->lastMessage = touch($this->getFilename(), $time, $atime);
        return $this;
    }

    public function chmod($mode)
    {
        $this->lastMessage = chmod($this->getFilename(), $mode);
        return $this;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    public function read($length)
    {
        return fread($this->handle, $length);
    }

    public function gets()
    {
        return fgets($this->handle);
    }

    public function close()
    {
        $this->flush();
        $this->lastMessage = fclose($this->handle);
        return $this;
    }

    public function rewind()
    {
        $this->lastMessage = rewind($this->handle);
        return $this;
    }

    public function getLineGenerator()
    {
        while (false !== ($line = $this->gets())) {
            yield $line;
        }
    }

    public static function isFile($filename)
    {
        return is_file($filename);
    }

    public static function delete($filename)
    {
        return unlink($filename);
    }

    public function unlink()
    {
        return self::delete($this->getFilename());
    }

    public function flush()
    {
        return fflush($this->handle);
    }
}
