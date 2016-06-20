<?php
namespace codeagent\treemap\presenter;

use \Closure;
use codeagent\treemap\IPresenter;
use codeagent\treemap\Treemap;
use codeagent\treemap\Rectangle;

abstract class Presenter implements IPresenter
{
    /**
     * @var Treemap
     */
    protected $treemap;

    /**
     * APresenter constructor.
     * @param Treemap $treemap
     */
    public function __construct(Treemap $treemap)
    {
        $this->treemap = $treemap;
    }

    /**
     * @param Closure|null $callback
     * @return string
     */
    public function render(Closure $callback = null)
    {
        $data = $this->treemap->getMap();
        $data = $this->format($data, $this->treemap);
        array_walk($data, [$this, 'nodeRenderrer']);
        if($callback) {
            array_walk($data, $callback);
        }
        return $this->renderInternal($data);
    }

    /**
     * @param NodeInfo[] $nodes
     * @return string
     */
    abstract protected function renderInternal(array $nodes);

    /**
     * @param array $nodes
     * @param Rectangle $parent
     * @param int $level
     * @return NodeInfo[]
     */
    protected function format(array $nodes, Rectangle $parent, $level = 0)
    {
        $childAttr = $this->treemap->childrenAttribute;
        $result    = [];
        foreach($nodes as $id => $node) {
            $data = $node;
            unset($data['_rectangle'], $data[$childAttr]);
            $isLeaf    = !isset($node[$childAttr]) || empty($node[$childAttr]);
            $rectangle = $node['_rectangle']->shift($parent->left, $parent->top);
            $info      = new NodeInfo($data, $id, $level, $isLeaf);

            $info->rectangle($rectangle);
            $info->background('#dddddd');

            $result[] = $info;

            if(!$isLeaf) {
                $result = array_merge($result, $this->format($node[$childAttr], $rectangle, $level + 1));
            }
        }
        return $result;
    }

    /**
     * @param NodeInfo $node
     */
    public function nodeRenderrer(NodeInfo $node)
    {
        if(!$node->isLeaf()) {
            $node->visible(false);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}