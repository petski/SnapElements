<?php
/**
 * Fluidics
 *
 * @version 0.2
 * @author Oliver Saunders <oliver.saunders@gmail.com>
 * @license GNU Lesser Public (see LICENSE file)
 */
/**
 * Get the file name and line that an error $steps_back in the stack occurred.
 * Designed for use in internal fluidics functions only.
 *
 * @param int $steps_back
 * @return array
 */
function flu_error_orgin($steps_back = 1)
{
    return mindex(index(debug_backtrace(), $steps_back), array('file', 'line'));
}
/**
 * Trigger a user-generated error with details of the true origin of the error
 *
 * @param string $msg description of the error
 * @param int $code as in 2nd param of trigger_error()
 * @param int $back_offset distance in the stack to look back for line number and path
 */
function flu_error($msg, $code, $back_offset = 1)
{
    list($file, $line) = flu_error_orgin(1 + $back_offset);
    trigger_error("$msg in $file on line $line", $code);
}
/**
 * Generate an undefined index error message. Designed for use in internal
 * fluidics functions only.
 *
 * @param mixed $key
 */
function flu_undefined_index_error($key)
{
    if (is_bool($key)) {
        $key = (int)$key;
    }
    flu_error("Notice: Undefined offset: $key", E_USER_NOTICE, 2);
}
/**
 * Returns one of an array's elements specified by a key. Errors are triggered
 * if the array element does not exist.
 *
 * $key may specified as an array for multi-dimensional access.
 *
 * @param array $array
 * @param mixed $key
 * @return mixed
 */
function index($array, $key)
{
    foreach ((array)$key as $dimension_key) {
        if (!isset($array[$dimension_key])) {
            flu_undefined_index_error($dimension_key);
            return null;
        }
        $array = $array[$dimension_key];
    }
    return $array;
}
/**
 * Returns one of an array's elements specified by a key. No errors are
 * triggered. If the array element does not exist $default is returned instead.
 *
 * $key may specified as an array for multi-dimensional access.
 *
 * @param array $array
 * @param mixed $key
 * @param mixed $default
 * @return mixed
 */
function dindex($array, $key, $default = null)
{
    foreach ((array)$key as $dimension_key) {
        if (!isset($array[$dimension_key])) {
            return $default;
        }
        $array = $array[$dimension_key];
    }
    return $array;
}
/**
 * Return multiple array elements specified by an array of keys. Errors are
 * triggered if array elements do not exist.
 *
 * @param array $array
 * @param array $keys
 * @param bool $preserve_keys
 * @return array
 */
function mindex($array, $keys, $preserve_keys = false)
{
    $out = array();
    foreach ($keys as $key) {
        if ($preserve_keys) {
            $out[$key] = index($array, $key);
        } else {
            $out[] = index($array, $key);
        }
    }
    return $out;
}
/**
 * Return multiple array elements specified by an array of keys. No errors are
 * triggered. If array elements do not exist the value of $default is used instead.
 *
 * @param array $array
 * @param array $keys
 * @param bool $preserve_keys
 * @return array
 */
function dmindex($array, $keys, $preserve_keys = false, $default = null)
{
    $out = array();
    foreach ($keys as $key) {
        if ($preserve_keys) {
            $out[$key] = dindex($array, $key, $default);
        } else {
            $out[] = dindex($array, $key, $default);
        }
    }
    return $out;
}
/**
 * Returns $obj
 *
 * @see README for why this is any use
 * @param mixed $obj
 * @return mixed
 */
function with($obj)
{
    return $obj;
}
/**
 * Like func_get_args() without restrictions
 *
 * @return array
 */
function args()
{
    return dindex(dindex(debug_backtrace(), 1), 'args');
}
/**
 * var_dump() only $var is returned
 *
 * @param mixed $var
 * @return mixed
 */
function dump($var)
{
    var_dump($var);
    return $var;
}
/**
 * Returns the output from var_dump($var)
 *
 * @param mixed $var
 * @return string
 */
function sdump($var)
{
    ob_start();
    var_dump($var);
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}
/**
 * Create an array from whitespace separated elements. Can save a lot of typing
 * in some instances.
 *
 * @param string $elements
 * @return array
 */
function w($elements)
{
    return preg_split('~\s+~', $elements, -1, PREG_SPLIT_NO_EMPTY);
}
/**
 * Version of create_function that hashes the code so that it only have to be
 * interpretted once if used, for instance, inside a loop.
 *
 * @param string $args
 * @param string $code
 * @return lambda
 */
function lamba($args, $code)
{
    static $cache = array();
    $hash = $args . $code;
    if (!isset($cache[$hash])) {
        $lambda = create_function($args, $code . ';');
        if (!$lambda) {
            throw new Exception('Error defining lambda function: ' . $code);
        }
        $cache[$hash] = $lambda;
    }
    return $cache[$hash];
}
/**
 * Executes $code over contents of $elements storing the results of $code in an
 * array and returning it. You may access the key and value of the current
 * iteration by using $k and $v respectively in $code.
 *
 * @see README for usage
 * @param unknown_type $elements
 * @param unknown_type $code
 * @param unknown_type $preserve_keys
 * @return unknown
 */
function umap($elements, $code, $preserve_keys = false)
{
    $lamda = lamba('$k, $v', $code);
    $out = array();
    if ($preserve_keys) {
        foreach ($elements as $k => $v) {
            $out[$k] = $lamda($k, $v);
        }
    } else {
        foreach ($elements as $k => $v) {
            $out[] = $lamda($k, $v);
        }
    }
    return $out;
}
/**
 * A combined getter and setter for use in objects.
 *
 * @see README for usage
 * @param object $obj $this
 * @param mixed &$property property to set and get
 * @param array $args
 * @return mixed
 */
function accessor($obj, &$property, array $args = array())
{
    if (empty($args)) {
        return $property;
    }
    $property = $args[0];
    return $obj;
}
/**
 * @deprecated I can't properly emulate the behaviour of empty
 */
function mempty()
{
    $args = func_get_args();
    foreach ($args as $arg) {
        if (empty($arg)) {
            return true;
        }
    }
    return false;
}
