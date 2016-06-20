<?php
namespace codeagent\treemap;

class Rectangle
{
    public $left;
    public $top;
    public $width;
    public $height;

    public function __construct()
    {
        $args = func_get_args();
        if(!count($args)) {
            $this->left = $this->top = $this->width = $this->height = 0;
        }
        elseif(count($args) == 2) {
            $this->left   = $this->top = 0;
            $this->width  = $args[0];
            $this->height = $args[1];
        }
        else {
            list($this->left, $this->top, $this->width, $this->height) = $args;
        }
    }

    public function aspect()
    {
        if($this->width == 0 || $this->height == 0)
            return PHP_INT_MAX;

        return max($this->width / $this->height, $this->height / $this->width);
    }

    public function shift($left, $top)
    {
        return new Rectangle($this->left + $left, $this->top + $top, $this->width, $this->height);
    }

}