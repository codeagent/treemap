<?php
namespace codeagent\treemap;

class Map extends Rectangle
{
    protected $margin;

    public function __construct($width, $height, $margin = 0)
    {
        parent::__construct($width, $height);
        $this->margin = $margin;
    }

    /**
     * @param number[] $values
     * @return Rectangle[]
     */
    public function squarify(array $values)
    {
        return $this->squarifyInternal($this, $values);
    }

    /**
     * @param Rectangle $boundaries
     * @param number[] $values
     * @return Rectangle[]
     */
    protected function squarifyInternal(Rectangle $boundaries, array $values)
    {
        if(empty($values))
            return [];

        reset($values);
        $curr = new Layout($boundaries, $values, $this->margin);
        $next = new Layout($boundaries, $values, $this->margin);
        $next->push(key($values));

        while($curr->worst() >= $next->worst() && count($values)) {
            // array_shift with preserving relation: key => value
            $curr->push(key($values));
            $values = array_slice($values, 1, count($values), true);
            if(count($values)) {
                $next->push(key($values));
            }
        }
        return $curr->items() + $this->squarifyInternal($curr->remaining(), $values);
    }
}