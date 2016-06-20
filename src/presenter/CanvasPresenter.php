<?php
namespace codeagent\treemap\presenter;

class CanvasPresenter extends Presenter
{
    /**
     * @var string
     */
    public static $font = 'Arial';

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
        $id = static::$id . "-" . ++static::$counter;
        return "<canvas id='{$id}' width='{$this->treemap->width}' height='{$this->treemap->height}'></canvas>
				{$this->clientScript($nodes, $id)}";
    }

    /**
     * @param NodeInfo[] $data
     * @param $id
     * @return string
     */
    protected function clientScript(array $data, $id)
    {
        $data = array_map([$this, 'mapNode'], $data);
        $data = array_values(array_filter($data));
        $data = json_encode($data);
        $font = static::$font;
        return "
<script>
(function (id, data) {
	var canvas = document.getElementById(id);
	if (canvas.getContext) {
		var ctx = canvas.getContext('2d');
		data.forEach(function (item) {
			ctx.fillStyle = item[4];
			ctx.fillRect(item[0], item[1], item[2], item[3]);
			item[5].forEach(function(line) {
				ctx.font = line[3] + 'px {$font}';
				ctx.fillStyle = line[4];
				ctx.textAlign = line[5];
				ctx.textBaseline = line[6];
				ctx.fillText(line[0], line[1], line[2]);	
			});
		});
	}
})('{$id}', {$data})
</script>";
    }

    /**
     * @param NodeInfo $info
     * @return array
     */
    protected function mapNode(NodeInfo $info)
    {
        if($info->visible()) {
            $content = $info->content()->content();
            foreach($content as $id => $line) {
                $content[$id][1] += $info->rectangle()->left;
                $content[$id][2] += $info->rectangle()->top;
            }

            return [
                $info->rectangle()->left,
                $info->rectangle()->top,
                $info->rectangle()->width,
                $info->rectangle()->height,
                $info->background(),
                $content,
                $info->level(),
                $info->isLeaf(),
                $info->isRoot()
            ];
        }
        else {
            return false;
        }
    }
}