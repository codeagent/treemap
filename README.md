Sometimes we need to convenient represent hierarchical data structures such as file system. 
One of the famous methods is a treemap. This **php package** provides you to build treemaps in formats of html, canvas and image from native php multiarrays. 
Special architecture of classes gives you an opportunity for customization at level of one node: node content, node color and other.

todo: [demo]

More info about treemaps you may see in [wiki](https://en.wikipedia.org/wiki/Treemapping). 

## Installation
Install the latest version via composer:

```
composer require codeagent\treemap
```

Also, it's necessary to plug in required css-styles to your page for correct display of html markup. 
You can do that by copying `treemap.css` file to web-accessible folder of your
server. To do that, simply run `vendor/bin/treemap` command with target directory as argument. Then you need to include it into the head section of page via `<link>` tag, 
for example `<link rel="stylesheet" href="assets/treemap.css" />`

## Basic usage
Include composer’s autoloader and inject `Treemap` class from `codeagent\treemap` namespace into php script. 
Map is waiting at the entrance your data as first argument, width and height as second and third arguments respectively. 
To obtain the result of rendering (html markup, image), call `render` method of `Presenter` interface:

```php
include("vendor/autoload.php");
use codeagent\treemap\Treemap;

// your data in consistent format
$data = [["value" => 2, "children" => [...]], ["value" => "4", "children" => [...]], [...], ...]; 
$presenter = Treemap::html($data, 1200, 800);
echo $presenter->render();
```

By default, Treemap class considers, that "weight" non-negative value are located in the `value` attribute and the
"children" nodes addressed via correspond `children` key of node. 
If it is not suitable for you, you can tell  treemap which keys to pick explicitly:

```php
$treemap = new Treemap($data, 1200, 800);
$treemap->valueAttribute = "volume";
$treemap->childrenAttribute = "department";
```

And then draw treemap using appropriate Presenter:

```php
$presenter = new HtmlPresenter($treemap);
echo $presenter->render();
```

Not forget to inject presenter class to you script via `use` statement.


## Advanced usage
### Formats of representation
Of course, presentation of treemap does not limited only by html. 
Besides html there are classes for building images and canvas elements in this package.

```php
include("vendor/autoload.php");
use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\presenter\NestedHtmlPresenter;
use codeagent\treemap\presenter\CanvasPresenter;
use codeagent\treemap\presenter\ImagePresenter;

$data = [...]; // your hierarhical data
const WIDTH = 1200;
const HEIGHT = 800;
$treemap = new Treemap($data, WIDTH, HEIGHT);

$html = (new HtmlPresenter($treemap))->render(); 
// same as  $html = Treemap::html($data, WIDTH, HEIGHT)->render();
echo $html;

$canvas = (new CanvasPresenter($treemap))->render(); 
// same as  $canvas = Treemap::canvas($data, WIDTH, HEIGHT)->render();
echo $canvas;

$image = (new ImagePresenter($treemap, "png"))->render();
// same as $image = Treemap::image($data, WIDTH, HEIGHT, "png")->render();

header("Content-Type: image/png");
echo $image;
```

`ImagePresenter` outputs image in form of raw data. 
For this reason, pass appropriate content-type header with image data: `header("Content-Type: image/png")`.
It is worth noting the `NestedtmlPresenter`. In fact, it is a collection of treemaps with different detalization degree, 
which gives you conveniently navigate from one map to another via headers and breadcrumbs at top of presenter.

### Custom node styles
Presenter interface gives an ability to adjust a single node rendering via a utility `Nodeinfo` class, for example:
```php
use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\presenter\NodeInfo;

$presenter = Treemap::html($data, $width, $height)
    ->render(function(NodeIndo $node){
        $data = $node->data();	
        $node->content()->html("<span>{$data['name']}</span>");
        $node->background("calculated_color_here");
    });
```

`NodeInfo` api provides an access to node information:

 - **background()** - sets/gets background color of node
 - **content()** - access to content of node (NodeContent)
 - **rectangle()** - access to geometry (rectangle) of node
 - **level()** - depth of node (0 is root Node)
 - **isLeaf()** - whether node is leaf
 - **isRoot()** - whether node is root
 - **id()** - order between node siblings
 - **visible()** - whether node is visible/not
 - **data()** - node data


## License
MIT
