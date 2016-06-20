<?php
namespace codeagent\treemap\presenter;

use Intervention\Image\ImageManagerStatic;
use Intervention\Image\Image;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use codeagent\treemap\Treemap;

class ImagePresenter extends Presenter
{
    /**
     * @var string
     */
    public static $fontPath = __DIR__ . "/../assets/OpenSans-Regular.ttf";

    /**
     * @var string
     */
    protected $format;

    public function __construct(Treemap $treemap, $format = 'png')
    {
        parent::__construct($treemap);
        $this->format = $format;
    }

    /**
     * @param NodeInfo[] $nodes
     * @return string
     */
    protected function renderInternal(array $nodes)
    {
        /** @var \Intervention\Image\Image $img */
        $img = ImageManagerStatic::canvas($this->treemap->width, $this->treemap->height);
        /** @var NodeInfo $node */
        foreach($nodes as $node) {
            if($node->visible()) {
                $rectangle = $node->rectangle();
                $img->rectangle(
                    $rectangle->left,
                    $rectangle->top,
                    $rectangle->left + $rectangle->width - 1,
                    $rectangle->top + $rectangle->height - 1,
                    function (AbstractShape $draw) use ($node) {
                        $draw->background($node->background());
                    });

                $this->writeContent($img, $node);
            }
        }
        return (string)$img->encode($this->format);
    }

    /**
     * @param Image $image
     * @param NodeInfo $node
     */
    protected function writeContent(Image $image, NodeInfo $node)
    {
        $content = $node->content();
        $rect    = $node->rectangle();
        foreach($content->content() as $line) {
            $image->text($line[0], $rect->left + $line[1], $rect->top + $line[2], function (AbstractFont $font) use ($line) {
                $font->color($line[4]);
                $font->size($line[3]);
                $font->align($line[5]);
                $font->valign($line[6]);
                $font->file(static::$fontPath);
            });
        }

    }
}