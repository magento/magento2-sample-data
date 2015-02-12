<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Helper\Csv;

/**
 * Class Reader
 */
class Reader implements \Iterator
{
    /**
     * @var array
     */
    protected $handle;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var bool
     */
    protected $loaded;

    /**
     * @var array
     */
    protected $headerRow;

    /**
     * @var array
     */
    protected $row;

    /**
     * @var int
     */
    protected $rowNumber;

    /**
     * @param string $fileName
     * @param string $mode
     */
    public function __construct($fileName, $mode = 'r')
    {
        $this->fileName = $fileName;
        $this->mode = $mode;
    }

    /**
     * @return $this
     */
    protected function load()
    {
        if (!$this->loaded) {
            $this->handle = fopen($this->fileName, $this->mode);
            $this->loaded = true;
            $this->rowNumber = 0;
            $this->readHeaderRow();
        }
        return $this;
    }

    /**
     * @return Reader
     */
    protected function reload()
    {
        $this->loaded = false;
        return $this->load();
    }

    /**
     * Read header row
     * @return void
     */
    protected function readHeaderRow()
    {
        $this->headerRow = $this->readRow();
        $this->next();
    }

    /**
     * @return array
     */
    public function getHeaderRow()
    {
        return $this->headerRow;
    }

    /**
     * @return array
     */
    protected function readRow()
    {
        $this->load();
        $this->row = fgetcsv($this->handle, null, ',');
        $this->rowNumber++;
        return $this->row;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $row = [];
        foreach ($this->getHeaderRow() as $index => $field) {
            $value = $this->row[$index];
            if (isset($row[$field])) {
                $row[$field] = is_array($row[$field]) ? $row[$field] : [$row[$field]];
                $value = is_array($value) ? $value : [$value];
                $row[$field] =  array_merge($row[$field], $value);
            } else {
                $row[$field] = $value;
            }
        }
        return $row;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->readRow();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->rowNumber;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $isValid = (bool)$this->row;

        if (!$isValid) {
            $this->close();
        }

        return $isValid;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->reload();
    }

    /**
     * Close file handle
     * @return void
     */
    public function close()
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
    }

    /**
     * Destroy file handle
     */
    public function __destruct()
    {
        $this->close();
    }
}
