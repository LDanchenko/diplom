<?php


class KeyValueMapStorage implements Countable, Iterator, ArrayAccess {
    private $storageKeys; //обьекты класса
    private $storageValues; //коефициенты - массив?

    public function __construct() {
        $this->storageKeys = new SplObjectStorage();
        $this->storageValues = array();
    }

    function append($keyObject, $value) {
        $value = (string)$value;
        $this->storageKeys[$keyObject] = $value;

        if (!array_key_exists($value, $this->storageValues)) {
            $this->storageValues[$value] = array();
        }
        $this->storageValues[$value][] = $keyObject;
    }

    function remove($keyObject) {
        $value = $this[$keyObject];
        if (!array_key_exists($value, $this->storageValues)) {
            throw new Exception('Data was not integrity.');
        }

        if (($key = array_search($keyObject, $this->storageValues[$value])) !== false) {
            unset($this->storageValues[$value][$key]);
        }
    }

    function removeAllKeysByValue($value) {
        $value = (string)$value;
        if (!array_key_exists($value, $this->storageValues)) {
            return;
        }

        $keys = $this->storageValues[$value];
        foreach ($keys as $key) {
            unset($this->storageKeys[$key]);
        }

        $this->storageValues[$value] = array();
    }

    function getValueByKey($key) {
        return $this->storageKeys->offsetGet($key);
    }

    function getKeysByValue($value) {
        $value = (string)$value;
        if (!array_key_exists($value, $this->storageValues)) {
            return array();
        }

        return $this->storageValues[$value];
    }
//$n - пар для распеределения , из times. ????
    public function topByValue($n, $randomize = false) {
        $result = new KeyValueMapStorage();
        $sortedValues = array_keys($this->storageValues); // ключи по коеф?
        rsort($sortedValues); //от большего к меньшему

        $currentIndex = 0;
        $resultSize = 0;
        $possibleValuesVariants = count($sortedValues);

        while ($resultSize <= $n && $currentIndex < $possibleValuesVariants) {
            $value = $sortedValues[$currentIndex];
            $keysObject = $this->getKeysByValue($value);

            if ($randomize === true) {
                shuffle($keysObject);
            }

            foreach ($keysObject as $keyObject) {
                $result->append($keyObject, $value);
                $resultSize++;

                if ($resultSize >= $n) {
                    break;
                }
            }

            $currentIndex++;
        }

        return $result;
    }

    public function extend(KeyValueMapStorage $storage) {
        foreach ($storage as $item) {
            list($key, $value) = $item;
            $this->append($key, $value);
        }
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() {
        return $this->storageKeys->count();
    }


    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        $keyObject = $this->storageKeys->current();
        $value = $this->getValueByKey($keyObject);

        return array($keyObject, $value);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        $this->storageKeys->next();
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return $this->storageKeys->key();
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return $this->storageKeys->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        $this->storageKeys->rewind();
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return $this->storageKeys->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->storageKeys->offsetGet($offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->append($offset, $value);
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }
}
