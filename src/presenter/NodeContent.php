<?php
namespace codeagent\treemap\presenter;

class NodeContent
{
    const ALIGN_LEFT   = 'left';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT  = 'right';

    const VALIGN_TOP    = 'top';
    const VALIGN_MIDDLE = 'middle';
    const VALIGN_BOTTOM = 'bottom';

    public static $defaultSize   = 12;
    public static $defaultColor  = '#000000';
    public static $defaultAlign  = self::ALIGN_LEFT;
    public static $defaultValign = self::VALIGN_TOP;

    protected $content = [];
    protected $html    = '';
    private   $color;
    private   $size;
    private   $align;
    private   $valign;

    public function text($text, $x = 0, $y = 0)
    {
        $this->content[] = [$text, $x, $y, $this->size, $this->color, $this->align, $this->valign];
        $this->color     = static::$defaultColor;
        $this->size      = static::$defaultSize;
        $this->align     = static::$defaultAlign;
        $this->valign    = static::$defaultValign;
    }

    public function color($color)
    {
        $this->color = $color;
        return $this;
    }

    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    public function align($align)
    {
        $this->align = $align;
        return $this;
    }

    public function valign($valign)
    {
        $this->valign = $valign;
        return $this;
    }

    public function content()
    {
        return $this->content;
    }

    public function html($html = null)
    {
        if($html) {
            $this->html = $html;
        }
        else {
            return $this->html;
        }
    }

}