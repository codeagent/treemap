<?php
namespace codeagent\treemap\presenter;

use \Closure;
use codeagent\treemap\IPresenter;
use codeagent\treemap\Treemap;
use codeagent\treemap\Rectangle;

class NestedHtmlPresenter implements IPresenter
{
    /**
     * @var string
     */
    public static $id = 'nested-treemap';

    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * @var int
     */
    public $captionHeight = 20;

    /**
     * @var string
     */
    public $name = 'Map';

    /**
     * @var string
     */
    public $nameAttribute = 'name';

    /**
     * @var Treemap
     */
    protected $treemap;

    public function __construct(Treemap $treemap)
    {
        $this->treemap = $treemap;
    }

    /**
     * @param \Closure|null $callback
     * @return string
     */
    public function render(Closure $callback = null)
    {
        $this->treemap->setNodeFrameResolver([$this, 'resolveNodeFrame']);

        $renderrer = [$this, 'nodeRenderrer'];
        if($callback) {
            $renderrer = function (NodeInfo $node, $mapId) use ($callback) {
                call_user_func([$this, 'nodeRenderrer'], $node, $mapId);
                call_user_func($callback, $node, $mapId);
            };
        }

        $id              = HtmlPresenter::$id . '-r';
        $presenters      = $this->getPresenters($this->treemap, $id);
        $presenters[$id] = new HtmlPresenter($this->treemap);
        $content         = implode("", $this->renderPresenters($presenters, $renderrer));
        $id              = static::$id . "-" . ++static::$counter;
        return "<div id='{$id}'><nav><a href='#'>{$this->name}</a></nav>{$content}</div>{$this->getClientScript($id, $this->name)}";
    }

    /**
     * @param Treemap $root
     * @param $id
     * @return array
     */
    protected function getPresenters(Treemap $root, $id)
    {
        $presenters = [];
        foreach($root->getMap() as $key => $node) {
            if(isset($node[$root->childrenAttribute]) && !empty($node[$root->childrenAttribute])) {
                $map                    = new Treemap($node[$root->childrenAttribute], $root->width, $root->height);
                $map->valueAttribute    = $root->valueAttribute;
                $map->childrenAttribute = $root->childrenAttribute;
                $map->setNodeFrameResolver([$this, 'resolveNodeFrame']);

                $key              = "{$id}-{$key}";
                $presenter        = new HtmlPresenter($map);
                $presenters[$key] = $presenter;
                $presenters       = array_merge($presenters, $this->getPresenters($map, $key));
            }
        }
        return $presenters;
    }

    /**
     * @param array $presenters
     * @param callable $callback
     * @return array
     */
    protected function renderPresenters(array $presenters, callable $callback)
    {
        $html = [];
        foreach($presenters as $id => $presenter) {
            $renderrer = function ($node) use ($id, $callback) {
                return call_user_func($callback, $node, $id);
            };
            $html[]    = "<div id='{$id}'>{$presenter->render($renderrer)}</div>";
        }
        return $html;
    }

    /**
     * @param Rectangle $boundaries
     * @param $level
     * @return Rectangle
     */
    public function resolveNodeFrame(Rectangle $boundaries, $level)
    {
        $margin = 10;
        if($level == 1) {
            return new Rectangle(
                $boundaries->left + $margin / 2,
                $boundaries->top + $margin / 2 + $this->captionHeight,
                $boundaries->width - $margin,
                $boundaries->height - $margin - $this->captionHeight
            );
        }
        else {
            return $boundaries;
        }
    }

    /**
     * @param NodeInfo $node
     * @param string $mapId
     */
    public function nodeRenderrer(NodeInfo $node, $mapId)
    {
        if($node->isLeaf()) {
            $data = $node->data();
            $node->content()->html("<span style='line-height:{$node->rectangle()->height}px'>{$data[$this->nameAttribute]}</span>");
        }
        elseif($node->level() == 0) {
            $node->visible(true);
            $data = $node->data();
            $node->content()->html("<a title='" . htmlspecialchars($data[$this->nameAttribute]) . "' href='#{$mapId}-{$node->id()}'>{$data[$this->nameAttribute]}</a>");
            $node->background('transparent');
        }
        else {
            $node->visible(false);
        }
    }

    /**
     * @param $id
     * @param $name
     * @return string
     */
    protected function getClientScript($id, $name)
    {
        return "<script>
	(function (id, name) {
		var container = document.getElementById(id),
			breadcrumbs = container.getElementsByTagName('nav')[0],
			maps = array(document.querySelectorAll(\"#\" + id + \" > div\"));

		function array(collection) {
			return Array.prototype.slice.call(collection, 0);
		}

		function navigate(to) {
			var target = container.querySelector(to);
			maps.forEach(function (e) {
				e.style.display = 'none';
			});
			target.style.display = 'block';
		}

		function links(target) {
			var match = target.match(/r(-\d+)*$/),
				path = match[0].split(\"-\"),
				prefix = target.substring(0, match.index - 1);

			var links = [];
			while (path.length) {
				prefix = prefix + \"-\" + path.shift();
				var element = document.querySelector(\"#\" + id + ' > div [href=\"' + prefix + '\"]'),
					title = element ? element.getAttribute('title') : name;
				links.push(\"<a href='\" + prefix + \"'>\" + title + \"</a>\");
			}
			breadcrumbs.innerHTML = links.join(\"\");
		}

		container.addEventListener('click', function (e) {
			if (e.target.nodeName == 'A') {
				var target = e.target.getAttribute('href');
				if(target != '#') {
					navigate(target);
					links(target);
				}
				e.preventDefault();
				return false;
			}
		});

	})('{$id}', '{$name}');
</script>";
    }
}