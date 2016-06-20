<?php
namespace codeagent\treemap;

const COLOR_BLACK = 0x000000;
const COLOR_WHITE = 0xffffff;
const COLOR_GRAY  = 0x444444;
const COLOR_RED   = 0xff0000;
const COLOR_GREEN = 0x00ff00;
const COLOR_BLUE  = 0x0000ff;


/**
 * @param int $hex
 * @return int
 */
function red($hex)
{
    return ($hex & COLOR_RED) >> 16;
}

/**
 * @param int $hex
 * @return int
 */
function green($hex)
{
    return ($hex & COLOR_GREEN) >> 8;
}

/**
 * @param int $hex
 * @return int
 */
function blue($hex)
{
    return $hex & COLOR_BLUE;
}

/**
 * @param int $r
 * @param int $g
 * @param int $b
 * @return int
 */
function color($r, $g, $b)
{
    return $r << 16 | $g << 8 | $b;
}

/**
 * @param $str
 * @return int
 */
function str2hex($str)
{
    $str = str_replace("#", "", $str);

    if(strlen($str) == 3) {
        $r = hexdec(substr($str, 0, 1) . substr($str, 0, 1));
        $g = hexdec(substr($str, 1, 1) . substr($str, 1, 1));
        $b = hexdec(substr($str, 2, 1) . substr($str, 2, 1));
        return color($r, $g, $b);
    }
    else {
        return hexdec($str);
    }
}

/**
 * @param int $hex
 * @return string
 */
function hex2str($hex)
{
    $str = "#";
    $str .= str_pad(dechex(red($hex)), 2, "0", STR_PAD_LEFT);
    $str .= str_pad(dechex(green($hex)), 2, "0", STR_PAD_LEFT);
    $str .= str_pad(dechex(blue($hex)), 2, "0", STR_PAD_LEFT);

    return $str;
}

/**
 * @param int $color1
 * @param int $color2
 * @param float $factor
 * @return int
 */
function interpolate($color1, $color2, $factor)
{
    $r = (red($color2) - red($color1)) * $factor + red($color1);
    $g = (green($color2) - green($color1)) * $factor + green($color1);
    $b = (blue($color2) - blue($color1)) * $factor + blue($color1);

    return color($r, $g, $b);
}

/**
 * @param Rectangle $rectangle
 * @param int $precision
 * @param int $mode
 * @return Rectangle
 */
function round(Rectangle $rectangle, $precision = 0, $mode = PHP_ROUND_HALF_UP)
{
    $left   = \round($rectangle->left, $precision, $mode);
    $top    = \round($rectangle->top, $precision, $mode);
    $right  = \round($rectangle->left + $rectangle->width, $precision, $mode);
    $bottom = \round($rectangle->top + $rectangle->height, $precision, $mode);

    return new Rectangle($left, $top, $right - $left, $bottom - $top);
}