<?php
namespace codeagent\treemap\presenter;

use codeagent\treemap\Rectangle;

class NodeInfo
{
    protected $id;
    protected $data;
    protected $level;
    protected $isLeaf;
    protected $background;
    protected $content;
    protected $rectangle;
    protected $visible;

    public function __construct(array $data, $id, $level, $leaf)
    {
        $this->data    = $data;
        $this->id      = $id;
        $this->level   = $level;
        $this->isLeaf  = $leaf;
        $this->content = new NodeContent();
        $this->visible = true;
    }

    public function background($color = null)
    {
        if($color) {
            $this->background = is_string($color) ? $color : \codeagent\treemap\hex2str($color);
        }
        else {
            return $this->background;
        }
    }

    /**
     * @return NodeContent
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param Rectangle|null $rectangle
     * @return Rectangle|void
     */
    public function rectangle(Rectangle $rectangle = null)
    {
        if($rectangle) {
            $this->rectangle = $rectangle;
        }
        else {
            return $this->rectangle;
        }
    }

    public function level()
    {
        return $this->level;
    }

    public function isLeaf()
    {
        return $this->isLeaf;
    }

    public function isRoot()
    {
        return $this->level == 0;
    }

    public function id()
    {
        return $this->id;
    }

    public function visible($visible = null)
    {
        if($visible !== null) {
            $this->visible = $visible;
        }
        else {
            return $this->visible;
        }
    }

    /**
     * @return array
     */
    public function data()
    {
        return $this->data;
    }
}
