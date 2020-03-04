<?php

namespace app\threads;

class DataProvider extends \Threaded
{
    public $counters = [];

    public $data = [];

    /**
     * Set data for current key
     * @param $key
     * @param $data
     */
    public function set($key, $data)
    {
        if (is_array($data)) {
            $this->data[$key] = json_encode($data);
        } else {
            $this->data[$key] = $data;
        }
    }

    /**
     * Get data for current key
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $decoded = json_decode($this->data[$key], true);
        return ($decoded) ?: $this->data[$key];
    }

    /**
     * Get and remove all data for current key
     * @param $key
     * @return mixed
     */
    public function getAndRemove($key)
    {
        $decoded = json_decode($this->data[$key], true);
        $this->data[$key] = [];
        return $decoded;
    }

    /**
     * Cast Volatile to array and add to provider
     * @param $key
     * @param $data
     */
    public function add($key, $data)
    {
        $tmp = (array) $this->data[$key];
        $tmp[] = json_encode($data);
        $this->data[$key] = $tmp;
    }

    /**
     * Get last and remove from Volatile
     * @param $key
     * @return mixed
     */
    public function getLastAndRemove($key)
    {
        $tmp = (array) $this->data[$key];
        $element = array_pop($tmp);
        $this->data[$key] = $tmp;
        $decoded = json_decode($element, true);
        return $decoded;
    }

    /**
     * Increment count for current key of Volatile
     * @param $key
     */
    public function inc($key)
    {
        if (!isset($this->counters[$key])) {
            $this->counters[$key] = 0;
        }
        $tmp = (array) $this->counters;
        $tmp[$key] += 1;
        $this->counters[$key] = $tmp[$key];
    }
}
