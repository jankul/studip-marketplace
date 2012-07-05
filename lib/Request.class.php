<?php
# Lifter007: TODO
# Lifter003: TODO
/*
 * Request.php - class representing a HTTP request in Stud.IP
 *
 * Copyright (c) 2009  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 * Singleton class representing a HTTP request in Stud.IP.
 */
class Request implements ArrayAccess, IteratorAggregate
{
    /**
     * cached request parameter array
     */
    private $params;

    /**
     * Initialize a new Request instance.
     */
    private function __construct ()
    {
        $params = array_merge($_GET, $_POST);

        foreach ($params as $key => $value) {
            $this->params[$key] = self::removeMagicQuotes($value);
        }
    }

    /**
     * Return the Request singleton instance.
     */
    public static function getInstance ()
    {
        static $instance;

        if (isset($instance)) {
            return $instance;
        }

        return $instance = new Request();
    }

    /**
     * ArrayAccess: Check whether the given offset exists.
     */
    public function offsetExists ($offset)
    {
        return isset($this->params[$offset]);
    }

    /**
     * ArrayAccess: Get the value at the given offset.
     */
    public function offsetGet ($offset)
    {
        return $this->params[$offset];
    }

    /**
     * ArrayAccess: Set the value at the given offset.
     */
    public function offsetSet ($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    /**
     * ArrayAccess: Delete the value at the given offset.
     */
    public function offsetUnset ($offset)
    {
        unset($this->params[$offset]);
    }

    /**
     * IteratorAggregate: Create interator for request parameters.
     */
    public function getIterator ()
    {
        return new ArrayIterator($this->params);
    }

    /**
     * Return the current URL, including query parameters.
     */
    public static function url ()
    {
        return self::protocol().'://'.self::server().self::path();
    }

    /**
     * Return the current protocol ('http' or 'https').
     */
    public static function protocol ()
    {
        return $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    }

    /**
     * Return the current server name and port (host:port).
     */
    public static function server ()
    {
        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $ssl  = $_SERVER['HTTPS'] == 'on';

        if ($ssl && $port != 443 || !$ssl && $port != 80) {
            $host .= ':'.$port;
        }

        return $host;
    }

    /**
     * Return the current request path, relative to the server root.
     */
    public static function path ()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Set the selected query parameter to a specific value.
     *
     * @param string $param    parameter name
     * @param mixed  $value    parameter value (string or array)
     */
    public static function set ($param, $value)
    {
        $request = self::getInstance();

        $request->params[$param] = $value;
    }

    /**
     * Return the value of the selected query parameter as a string.
     *
     * @param string $param    parameter name
     * @param string $default  default value if parameter is not set
     *
     * @return string  parameter value as string (if set), else NULL
     */
    public static function get ($param, $default = NULL)
    {
        $request = self::getInstance();
        $value = $request->params[$param];

        if (!isset($value) || !is_string($value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Return the value of the selected query parameter as a string.
     * The contents of the string is quoted with addslashes().
     *
     * @param string $param    parameter name
     * @param string $default  default value if parameter is not set
     *
     * @return string  parameter value as string (if set), else NULL
     */
    public static function quoted ($param, $default = NULL)
    {
        $value = self::get($param, $default);

        if (isset($value)) {
            $value = addslashes($value);
        }

        return $value;
    }

    /**
     * Return the value of the selected query parameter as an alphanumeric
     * string (consisting of only digits, letters and underscores).
     *
     * @param string $param    parameter name
     * @param string $default  default value if parameter is not set
     *
     * @return string  parameter value as string (if set), else NULL
     */
    public static function option ($param, $default = NULL)
    {
        $value = self::get($param, $default);

        if (!isset($value) || preg_match('/\\W/', $value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Return the value of the selected query parameter as an integer.
     *
     * @param string $param    parameter name
     * @param int    $default  default value if parameter is not set
     *
     * @return int     parameter value as integer (if set), else NULL
     */
    public static function int ($param, $default = NULL)
    {
        $value = self::get($param, $default);

        if (isset($value)) {
            $value = (int) $value;
        }

        return $value;
    }

    /**
     * Return the value of the selected query parameter as a float.
     *
     * @param string $param    parameter name
     * @param float  $default  default value if parameter is not set
     *
     * @return float   parameter value as float (if set), else NULL
     */
    public static function float ($param, $default = NULL)
    {
        $value = self::get($param, $default);

        if (isset($value)) {
            $value = (float) strtr($value, ',', '.');
        }

        return $value;
    }

    /**
     * Return the value of the selected query parameter as an array.
     *
     * @param string $param    parameter name
     *
     * @return array  parameter value as array (if set), else an empty array
     */
    public static function getArray ($param)
    {
        $request = self::getInstance();
        $array = $request->params[$param];

        if (!isset($array) || !is_array($array)) {
            $array = array();
        }

        return $array;
    }

    /**
     * Return the value of the selected query parameter as a string array.
     * The contents of each element is quoted with addslashes().
     *
     * @param string $param    parameter name
     *
     * @return array  parameter value as array (if set), else an empty array
     */
    public static function quotedArray ($param)
    {
        $array = self::getArray($param);

        return self::addslashes($array);
    }

    /**
     * Return the value of the selected query parameter as an array of
     * alphanumeric strings (consisting of only digits, letters and
     * underscores).
     *
     * @param string $param    parameter name
     *
     * @return array  parameter value as array (if set), else an empty array
     */
    public static function optionArray ($param)
    {
        $array = self::getArray($param);

        foreach ($array as $key => $value) {
            if (preg_match('/\\W/', $value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Return the value of the selected query parameter as an integer array.
     *
     * @param string $param    parameter name
     *
     * @return array  parameter value as array (if set), else an empty array
     */
    public static function intArray ($param)
    {
        $array = self::getArray($param);

        foreach ($array as $key => $value) {
            $array[$key] = (int) $value;
        }

        return $array;
    }

    /**
     * Return the value of the selected query parameter as a float array.
     *
     * @param string $param    parameter name
     *
     * @return array  parameter value as array (if set), else an empty array
     */
    public static function floatArray ($param)
    {
        $array = self::getArray($param);

        foreach ($array as $key => $value) {
            $array[$key] = (float) strtr($value, ',', '.');
        }

        return $array;
    }

    /**
     * Check whether a form submit button has been pressed. This works for
     * both image and text submit buttons.
     *
     * @param string $param    submit button name
     *
     * @returns boolean  true if the button has been submitted, else false
     */
    public static function submitted ($param)
    {
        $request = self::getInstance();
        $value   = $request->params[$param];
        $value_x = $request->params[$param.'_x'];

        return isset($value) || isset($value_x);
    }

    /**
     * Quote a given string or array using addslashes(). If the parameter
     * is an array, the quoting is applied recursively.
     *
     * @param mixed $value    string or array value to be quoted
     *
     * @return mixed  quoted string or array
     */
    public static function addslashes ($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::addslashes($val);
            }
        } else {
            $value = addslashes($value);
        }

        return $value;
    }

    /**
     * Strip magic quotes from a given string or array. If the PHP setting
     * "magic_quotes_gpc" is enabled, stripslashes() is used on the value.
     * If the parameter is an array, magic quoting is stripped recursively.
     *
     * @param mixed $value    string or array value to be unquoted
     *
     * @return mixed  unquoted string or array
     */
    public static function removeMagicQuotes ($value)
    {
        if (get_magic_quotes_gpc()) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $value[$key] = self::removeMagicQuotes($val);
                }
            } else {
                $value = stripslashes($value);
            }
        }

        return $value;
    }
}
