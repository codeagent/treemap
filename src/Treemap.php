<?php
namespace codeagent\treemap;

use codeagent\treemap\presenter\CanvasPresenter;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\presenter\ImagePresenter;
use codeagent\treemap\presenter\NestedHtmlPresenter;

class Treemap extends Rectangle
{
    public static $cellSpacing = 1;

    protected $data;
    protected $map;

    public $valueAttribute    = 'value';
    public $childrenAttribute = 'children';

    /**
     * @var callable
     */
    protected $nodeFrameResolver;

    /**
     * @param array $data
     * @param $width
     * @param $height
     * @return HtmlPresenter
     */
    public static function html(array $data, $width, $height)
    {
        return new HtmlPresenter(new static($data, $width, $height));
    }

    /**
     * @param array $data
     * @param $width
     * @param $height
     * @return NestedHtmlPresenter
     */
    public static function nested(array $data, $width, $height)
    {
        return new NestedHtmlPresenter(new static($data, $width, $height));
    }

    /**
     * @param array $data
     * @param $width
     * @param $height
     * @return CanvasPresenter
     */
    public static function canvas(array $data, $width, $height)
    {
        return new CanvasPresenter(new static($data, $width, $height));
    }

    /**
     * @param array $data
     * @param $width
     * @param $height
     * @param string $format
     * @return ImagePresenter
     */
    public static function image(array $data, $width, $height, $format = 'png')
    {
        return new ImagePresenter(new static($data, $width, $height, $format));
    }

    /**
     * Treemap constructor.
     * @param $width
     * @param $height
     * @param array $data
     */
    public function __construct(array $data, $width, $height)
    {
        parent::__construct($width, $height);
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        if(is_null($this->map)) {
            $this->map = $this->map($this->data, $this);
        }

        return $this->map;
    }

    /**
     * @param callable $resolver
     */
    public function setNodeFrameResolver(callable $resolver)
    {
        $this->nodeFrameResolver = $resolver;
    }

    /**
     * @param array $data
     * @param Rectangle $boundaries
     * @param int $level
     * @return array
     */
    protected function map(array $data, Rectangle $boundaries, $level = 0)
    {
        $data = $this->mapRectangles($data, $boundaries, $level);

        foreach($data as $id => $item) {
            if(isset($item[$this->childrenAttribute]) && !empty($item[$this->childrenAttribute])) {
                $data[$id][$this->childrenAttribute] = $this->map($item[$this->childrenAttribute], $item['_rectangle'], $level + 1);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @param Rectangle $boundaries
     * @param $level
     * @return array
     */
    protected function mapRectangles(array $data, Rectangle $boundaries, $level)
    {
        $container = $this->nodeFrameResolver ? call_user_func($this->nodeFrameResolver, $boundaries, $level) : $boundaries;
        $dLeft     = $container->left - $boundaries->left;
        $dTop      = $container->top - $boundaries->top;
        $key       = $this->valueAttribute;
        $map       = new Map($container->width, $container->height, static::$cellSpacing);
        $values    = array_map(function ($item) use ($key) {
            return abs($item[$key]);
        }, $data);
        $values    = array_filter($values);
        arsort($values);

        $new = [];
        foreach($map->squarify($values) as $id => $rectangle) {
            $data[$id]['_rectangle'] = round($rectangle)->shift($dLeft, $dTop);
            $new[]                   = $data[$id];
        }
        return $new;
    }
}