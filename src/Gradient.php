<?php

namespace codeagent\treemap;

class Gradient
{
    public static $default = [
        '0.0' => 0x888888,
        '1.0' => 0xbbbbbb
    ];

    protected $gradient;

    public function __construct(array $gradient)
    {
        $this->setGradient($gradient);
    }

    /**
     * @param number $factor
     * @return string
     */
    public function color($factor)
    {
        $factor = max(0.0, min(1.0, $factor));
        $color1 = $color2 = $this->gradient['0.0'];
        $f1     = 0;

        foreach($this->gradient as $f => $color) {
            if($factor <= $f) {
                $color2 = $color;
                if($f1 != $f)
                    $factor = ($factor - $f1) / ($f - $f1);
                else
                    $factor = $f;
                break;
            }
            $color1 = $color;
            $f1     = $f;
        }

        return hex2str(interpolate($color1, $color2, $factor));
    }

    /**
     * @param array $gradient
     */
    public function setGradient(array $gradient)
    {
        if(!array_key_exists('0.0', $gradient))
            $gradient['0.0'] = COLOR_BLACK;

        if(!array_key_exists('1.0', $gradient))
            $gradient['1.0'] = COLOR_BLACK;

        foreach($gradient as $id => $color) {
            if(is_string($color))
                $gradient[$id] = str2hex($color);
        }

        $this->gradient = $gradient;
    }
}