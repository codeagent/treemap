<?php
namespace codeagent\treemap\demo;

use codeagent\treemap\Rectangle;
use codeagent\treemap\Gradient;
use codeagent\treemap\presenter\NodeInfo;
use codeagent\treemap\presenter\NodeContent;

class Utils
{
    public static function min($data, $attribute)
    {
        reset($data);
        $min = current($data);
        $min = $min[$attribute];

        foreach($data as $id => $item) {

            if($item[$attribute] < $min)
                $min = $item[$attribute];

            if(isset($item['children']) && !empty($item['children'])) {
                $m = static::min($item['children'], $attribute);
                if($m < $min)
                    $min = $m;
            }
        }
        return $min;
    }

    public static function max($data, $attribute)
    {
        reset($data);
        $max = current($data);
        $max = $max[$attribute];
        foreach($data as $id => $item) {

            if($item[$attribute] > $max)
                $max = $item[$attribute];

            if(isset($item['children']) && !empty($item['children'])) {
                $m = static::max($item['children'], $attribute);
                if($m > $max)
                    $max = $m;
            }
        }
        return $max;
    }

    public static function gradient($index)
    {
        $collection = [
            new Gradient(['0.0' => 0xf63538, '0.5' => '#414554', '1.0' => '#30cc5a']),
            new Gradient(['0.0' => 0x888888, '1.0' => '#30cc5a']),
            new Gradient(['0.0' => 0xaa0000, '1.0' => 0x0000aa])
        ];
        return $collection[$index];
    }

    public static function frame($margin = 5)
    {
        return function (Rectangle $rect, $level) use ($margin) {
            if($level == 1) {
                return new Rectangle($rect->left + $margin, $rect->top + $margin, $rect->width - $margin * 2, $rect->height - $margin * 2);
            }
            return $rect;
        };
    }

    public static function renderrer($gradient, $min, $max)
    {
        return function (NodeInfo $node) use ($gradient, $min, $max) {
            if($node->isLeaf()) {
                $data = $node->data();
                $node->background($gradient->color(($data['float'] - $min) / ($max - $min)));
                $node
                    ->content()
                    ->size(14)
                    ->color('#ffffff')
                    ->align(NodeContent::ALIGN_LEFT)
                    ->valign(NodeContent::VALIGN_TOP)
                    ->text($data['name']);
                $node
                    ->content()
                    ->size(10)
                    ->align(NodeContent::ALIGN_LEFT)
                    ->color($data['float'] > ($max + $min) / 2 ? '#00ff00' : '#ff0000')
                    ->text($data['float'], 0, 20);
            }
        };
    }

    public static function filesystem()
    {
        return include "data/filesystem.php";
    }

    public static function fake()
    {
        return include "data/data.php";
    }
}