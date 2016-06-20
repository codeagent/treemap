<?php

namespace codeagent\treemap;

class Layout
{
    /**
     * @var Rectangle
     */
    protected $boundaries;

    /**
     * @var int
     */
    protected $margin;

    /**
     * @var number[]
     */
    protected $values;

    /**
     * @var Rectangle[]
     */
    protected $items = [];

    public function __construct(Rectangle $boundaries, array $values, $margin = 0)
    {
        $this->boundaries = $boundaries;
        $this->margin     = $margin;
        $this->values     = $values;
    }

    /**
     * @return int
     */
    public function worst()
    {
        $worst = 0;
        foreach($this->items as $item)
            if($item->aspect() > $worst)
                $worst = $item->aspect();

        return $worst ?: PHP_INT_MAX;
    }

    /**
     * @return Rectangle
     */
    public function remaining()
    {
        if(!empty($this->items)) {
            $last = end($this->items);

            if($this->boundaries->width > $this->boundaries->height) {
                return new Rectangle(
                    $last->left + $last->width + $this->margin,
                    $this->boundaries->top,
                    max(0, $this->boundaries->left + $this->boundaries->width - $last->left - $last->width - $this->margin),
                    $this->boundaries->height
                );
            }
            else {
                return new Rectangle(
                    $this->boundaries->left,
                    $last->top + $last->height + $this->margin,
                    $this->boundaries->width,
                    max(0, $this->boundaries->height - $this->boundaries->top + $last->top - $last->height - $this->margin)
                );
            }
        }
        else {
            return new Rectangle(
                $this->boundaries->left,
                $this->boundaries->top,
                $this->boundaries->width,
                $this->boundaries->height
            );
        }
    }

    /**
     * @param number|string $key - index of value
     */
    public function push($key)
    {
        $this->items[$key] = new Rectangle();
        $sum               = array_sum(array_intersect_key($this->values, $this->items));

        if($this->boundaries->width > $this->boundaries->height) {

            $width  = $this->boundaries->width * $sum / array_sum($this->values);
            $height = max(0, $this->boundaries->height - (count($this->items) - 1) * $this->margin);
            $top    = 0;
            foreach($this->items as $id => $item) {
                $weight                   = $this->values[$id] / $sum;
                $h                        = $height * $weight;
                $this->items[$id]->left   = $this->boundaries->left;
                $this->items[$id]->top    = $this->boundaries->top + $top;
                $this->items[$id]->width  = $width;
                $this->items[$id]->height = $h;
                $top                      = $top + $h + $this->margin;
            }
        }

        else {
            $height = $this->boundaries->height * $sum / array_sum($this->values);
            $width  = max(0, $this->boundaries->width - (count($this->items) - 1) * $this->margin);
            $left   = 0;
            foreach($this->items as $id => $item) {
                $weight                   = $this->values[$id] / $sum;
                $w                        = $width * $weight;
                $this->items[$id]->left   = $this->boundaries->left + $left;
                $this->items[$id]->top    = $this->boundaries->top;
                $this->items[$id]->width  = $w;
                $this->items[$id]->height = $height;
                $left                     = $left + $w + $this->margin;
            }
        }
    }

    /**
     * @return Rectangle[]
     */
    public function items()
    {
        return $this->items;
    }

}