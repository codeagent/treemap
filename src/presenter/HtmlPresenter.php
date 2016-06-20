<?php
namespace codeagent\treemap\presenter;

class HtmlPresenter extends Presenter
{
    /**
     * @var int
     */
    public static $counter = 0;

    /**
     * @var string
     */
    public static $id = 'treemap';

    /**
     * @param NodeInfo[] $nodes
     * @return string
     */
    protected function renderInternal(array $nodes)
    {
        $content = implode("", array_filter(array_map([$this, 'renderNode'], $nodes)));
        $id      = static::$id . "-" . ++static::$counter;
        return "<div id='{$id}' style='width:{$this->treemap->width}px;height:{$this->treemap->height}px'>{$content}</div>";
    }

    /**
     * @param NodeInfo $node
     * @return string
     */
    protected function renderNode(NodeInfo $node)
    {
        if($node->visible()) {
            $rect    = $node->rectangle();
            $content = $this->contentHtml($node->content());
            return "<div style='left:{$rect->left}px;top:{$rect->top}px;width:{$rect->width}px;height:{$rect->height}px;background-color:{$node->background()}'>{$content}</div>";
        }
        else {
            return "";
        }
    }

    /**
     * @param NodeContent $content
     * @return string
     */
    protected function contentHtml(NodeContent $content)
    {
        $html = [$content->html()];
        foreach($content->content() as $row) {
            $html[] = "<span style='position:absolute;left:{$row[1]}px;top:{$row[2]}px;font-size:{$row[3]}px;color:{$row[4]};'>{$row[0]}</span>";
        }
        return implode("", $html);
    }
}