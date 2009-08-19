<?php
require_once dirname(__FILE__) . '/fluidics.php';
/**
 * Alias for htmlspecialchars()
 *
 * @param string $str
 * @return string
 */
function e($str)
{
    return htmlspecialchars($str);
}
/**
 * Returns signular or pural version of a word depending on whether $number is 1.
 * If $pural is not specificed an 's' is appended to singular.
 *
 * @param int $number
 * @param string $singluar
 * @param string|null $pural
 * @return string
 */
function flu_pural($number, $singluar, $pural = null)
{
    if (!is_int($number)) {
        throw new Exception('1st parameter (number) of ' . __FUNCTION__ . ' must be an integer');
    }
    if ($number === 1) {
        return $singluar;
    }
    return $pural ? $pural : rtrim($singluar) . 's';
}
/**
 * Returns english word for a number if between one and ten otherwise returns the
 * number as a string.
 *
 * @param int $number
 * @return string
 */
function flu_english_num($number)
{
	return dindex(array(
        1 => 'one', 'two',   'three', 'four',  'five',
             'six', 'seven', 'eight', 'nine',  'ten'
    ), (int)$number, (string)$number);
}
/**
 * Cleans the output buffer and returns it
 *
 * @return string
 */
function ob_end_get()
{
    if (ob_get_level()) {
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    flu_error('Output buffering not yet started', E_USER_WARNING);
}