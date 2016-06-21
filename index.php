<?php
include("vendor/autoload.php");

use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\NodeInfo;
use codeagent\treemap\presenter\NodeContent;
use codeagent\treemap\Gradient;
use codeagent\treemap\Rectangle;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\presenter\CanvasPresenter;
use codeagent\treemap\presenter\NestedHtmlPresenter;

/**
 * Header puzzle
 */
Treemap::$cellSpacing = 4;
$puzzle               = Treemap::html(include('data/puzzle.php'), 2100, 350)
    ->render(function (NodeInfo $node) {
        $data = $node->data();
        $node->background($data['color']);
        $node
            ->content()
            ->html();
    });
Treemap::$cellSpacing = 1;

/**
 * Content
 */
const WIDTH  = 850;
const HEIGHT = 600;
$data = include("data/data.php");


function human_size($bytes, $decimals = 2)
{
    $sz     = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/**
 * Basic usage
 */
$gradient = new Gradient(['0.0' => '#f75557', '0.5' => '#646a82', '1.0' => '#5ad87b']);
$basic    = Treemap::html($data, WIDTH, HEIGHT)->render();

/**
 * Custom node rendering
 */
$custom                   = Treemap::html($data, WIDTH, HEIGHT)->render(function (NodeInfo $node) use ($gradient) {
    if($node->isLeaf()) {
        $data   = $node->data();
        $max    = 5000;
        $min    = 10;
        $factor = ($data['float'] - $min) / ($max - $min);
        $color  = $gradient->color($factor);
        $node->content()->html("<span style='line-height: {$node->rectangle()->height}px'>{$data['name']}</span>");
        $node->background($color);
    }
});
$files                    = new Treemap(include("data/vendor.php"), WIDTH, HEIGHT);
$files->valueAttribute    = 'size';
$files->childrenAttribute = 'files';
$files->setNodeFrameResolver(function (Rectangle $rectangle, $level) {
    if($level == 1) {
        $titleHeight = 20;
        $spacing     = 4;
        return new Rectangle(
            $rectangle->left + $spacing,
            $rectangle->top + $titleHeight,
            $rectangle->width - $spacing,
            $rectangle->height - $titleHeight - $spacing
        );
    }
    return $rectangle;
});
$files = (new HtmlPresenter($files))->render(function (NodeInfo $node) {
    $data = $node->data();

    if($node->isLeaf()) {
        $node->visible(true);
        $node->content()->html("<span style='line-height: {$node->rectangle()->height}px'>{$data['name']}</span>");
    }
    elseif($node->isRoot()) {
        $node->visible(true);
        $node->background('transparent');
        $node
            ->content()
            ->html("<div style='width:100%;text-align:center'><strong>{$data['name']}</strong></div>");
    }
});

/**
 * Html
 */
$frameResolver             = function (Rectangle $rectangle, $level) {
    if($level == 1) {
        $titleHeight = 20;
        $spacing     = 4;
        return new Rectangle(
            $rectangle->left + $spacing / 2,
            $rectangle->top + $titleHeight + $spacing / 2,
            $rectangle->width - $spacing,
            $rectangle->height - $titleHeight - $spacing
        );
    }
    return $rectangle;
};
$renderrer                 = function (NodeInfo $node) {
    $data      = $node->data();
    $rectangle = $node->rectangle();
    if($node->isLeaf()) {
        $node->visible(true);
        $node
            ->content()
            ->size(11)
            ->align(NodeContent::ALIGN_CENTER)
            ->valign(NodeContent::VALIGN_BOTTOM)
            ->color('#333')
            ->text($data['name'], $rectangle->width / 2, $rectangle->height / 2);
        $node
            ->content()
            ->size(10)
            ->valign(NodeContent::VALIGN_TOP)
            ->align(NodeContent::ALIGN_CENTER)
            ->color("#2390EA")
            ->text(human_size($data['size']), $rectangle->width / 2, $rectangle->height / 2);

    }
    elseif($node->isRoot()) {
        $node->visible(true);
        $node->background('transparent');
        $node
            ->content()
            ->size(15)
            ->align(NodeContent::ALIGN_CENTER)
            ->valign(NodeContent::VALIGN_MIDDLE)
            ->color('black')
            ->text($data['name'], $rectangle->width / 2, 10);
    }
};
CanvasPresenter::$id       = "canvas";
$canvas                    = new Treemap(include("data/vendor.php"), WIDTH, HEIGHT);
$canvas->valueAttribute    = 'size';
$canvas->childrenAttribute = 'files';
$canvas->setNodeFrameResolver($frameResolver);
$canvas = (new CanvasPresenter($canvas))->render($renderrer);

/**
 * Nested
 */
$nested                    = new Treemap(include("data/vendor.php"), WIDTH, HEIGHT);
$nested->valueAttribute    = 'size';
$nested->childrenAttribute = 'files';
$nested                    = (new NestedHtmlPresenter($nested))->render();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Php treemap generator</title>
    <meta name="description" content="Generates treemaps for php">
    <meta name="keywords" content="treemap,php,map,nested,">
    <meta name="author" content="Alex Yakovlev">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/prism.css"/>
    <link rel="stylesheet" href="css/treemap.css"/>
    <link rel="stylesheet" href="css/site.css"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// --><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body>
<script src="js/prism.js"></script>
<header class="container-fluid">
    <div class="row" style="background: #70aef5"><?= $puzzle ?></div>
    <div class="container" id="brand">
        <div class="row">
            <h1>Treemap
                <small>php treemap generator</small>
            </h1>
        </div>
    </div>
</header>
<div class="container">
    <div class="row">
        <div class="col-xs-9">
            <h2 id="description">Description</h2>

            <p>This php package provides you to build treemaps in formats of html, canvas and image from native php
                multiarrays. Special architecture of classes gives you an opportunity for customization at level of one
                node: node content, node color and other.
            </p>

            <p>More info about treemaps you may see in
                <a href="https://en.wikipedia.org/wiki/Treemapping" target="_blank">wiki</a>.
            </p>

            <p class="text-justify">
                <a class="btn btn-primary inverse" href="https://github.com/codeagent/treemap">
                    <i class="fa fa-github"></i> View on github </a>
                <a class="btn btn-primary inverse" href="https://github.com/codeagent/treemap/zipball/master">
                    <i class="fa fa-file-archive-o"></i> Download .zip </a>
                <a class="btn btn-primary inverse" href="https://github.com/codeagent/treemap/tarball/master">
                    <i class="fa fa-file-archive-o"></i> Download .tar.zip </a>
            </p>

            <h2 id="installation">Installation</h2>

            <p>Install the latest version via composer:</p>

            <pre><code class="language-bash">composer require codeagent\treemap</code></pre>

            <p>
                Also, it's necessary to plug in required css-styles to your page for correct display of html markup. You
                can do that by copying treemap.css file to web-accessible folder of your server. For this, simply run
                <code class="language-bash">vendor/bin/treemap</code> command with target directory as argument. Then,
                you need to include it into the head section of page via &lt;link&gt; tag, for example
                <code class="language-markup">&lt;link rel="stylesheet" href="assets/treemap.css" /&gt;</code>
            </p>

            <h2 id="basic-usage">Basic usage</h2>
            <p>
                Include composer’s autoloader and inject <code class="language-php">Treemap</code> class from
                <code class="language-php">codeagent\treemap</code> namespace into php script. Map is waiting at the
                entrance your data as first argument, width and height as second and third arguments respectively. To
                obtain the result of rendering (html markup, image), call <code class="language-php">render</code>
                method of <code class="language-php">Presenter</code> interface:
            </p>

<pre><code class="language-php">include("vendor/autoload.php");
use codeagent\treemap\Treemap;

// your data in consistent format
$data = [["value" => 2, "children" => [...]], ["value" => 4, "children" => [...]], [...], ...];
$presenter = Treemap::html($data, 1200, 800);
echo $presenter->render();</code></pre>

            <p>The result of snippet would be something like this (based on your data):</p>

            <?= $basic ?>

            <p>
                By default, Treemap class considers, that <strong>"weight"</strong> (from which calcualted node
                rectangle) non-negative value are located in the value attribute and the <strong>"children"</strong>
                nodes addressed via correspond <strong>children</strong> key of node. If it is not suitable for you, you
                can tell treemap which keys to pick explicitly:
            </p>

<pre><code class="language-php">$treemap = new Treemap($data, 1200, 800);
$treemap->valueAttribute = "volume";
$treemap->childrenAttribute = "department";</code></pre>

            <p>And then draw treemap using appropriate <code class="language-php">Presenter</code>:</p>

<pre><code class="language-php">$presenter = new HtmlPresenter($treemap);
echo $presenter->render();</code></pre>

            <p>Not forget to inject presenter class to you script via <code class="language-php">use</code> statement.
            </p>

            <h2 id="node-rendering">Custom node rendering</h2>
            <p>
                There is no practical benefit of presenting treemap without accompanying data. For this reason there is
                <code class="language-php">Presenter</code> interface, that gives an ability to adjust a single node
                rendering via a utility <code class="language-php">Nodeinfo</code> class, for example, consider the
                folowing example:
            </p>
            <pre><code class="language-php">use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\NodeInfo;
use codeagent\treemap\Gradient;

$gradient = new Gradient(['0.0' => '#f75557', '0.5' => '#646a82', '1.0' => '#5ad87b']);
echo Treemap::html($data, WIDTH, HEIGHT)->render(function (NodeInfo $node) use ($gradient) {
    if($node->isLeaf()) {
        $data   = $node->data();
        $max    = 5000;
        $min    = 10;
        $factor = ($data['float'] - $min) / ($max - $min);
        $color  = $gradient->color($factor);
        $node->content()->html("&lt;span style='line-height: {$node->rectangle()->height}px'>{$data['name']}&lt;/span>");
        $node->background($color);
    }
});</code></pre>
            <p>The result is:</p>
            <?= $custom ?>
            <p>Another useful feature is customizing node frame calculating. Because of rectangles calculation specific,
                set up it in <code class="language-php">Treemap::setNodeFrameResolver(callable $resolver)</code> method.
                Pass to it a closure (or any callable you want) whitch returns new rectangle boundaries:</p>
                        <pre><code class="language-php">use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\Rectangle;
use codeagent\treemap\NodeInfo;

$files                    = new Treemap($data, WIDTH, HEIGHT);
$files->valueAttribute    = 'size';
$files->childrenAttribute = 'files';
$files->setNodeFrameResolver(function (Rectangle $rectangle, $level) {
    if($level == 1) {
        $titleHeight = 20;
        $spacing     = 4;
        return new Rectangle(
            $rectangle->left + $spacing,
            $rectangle->top + $titleHeight,
            $rectangle->width - $spacing,
            $rectangle->height - $titleHeight - $spacing
        );
    }
    return $rectangle;
});

echo (new HtmlPresenter($files))->render(function (NodeInfo $node) {
    $data = $node->data();

    if($node->isLeaf()) {
        $node->visible(true);
        $node->content()->html("&lt;span style='line-height: {$node->rectangle()->height}px'>{$data['name']}&lt;/span>");
    }
    elseif($node->isRoot()) {
        $node->visible(true);
        $node->background('transparent');
        $node
            ->content()
            ->html("&lt;div style='width:100%;text-align:center'>&lt;strong>{$data['name']}&lt;/strong>&lt;/div>");
    }
});
</code></pre>
            <?= $files ?>

            <h2 id="map-formats">Map formats</h2>
            <p>Of course, presentation of treemap does not limited only by html. Besides html there are classes for
                building images and canvas elements in this package.
            </p>
<pre><code class="language-php">use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\presenter\NestedHtmlPresenter;
use codeagent\treemap\presenter\CanvasPresenter;
use codeagent\treemap\presenter\ImagePresenter;

/**
 * @var HtmlPresenter $html
 */
$html = Treemap::html($data, WIDTH, HEIGHT);

/**
 * @var NestedHtmlPresenter $html
 */
$nested = Treemap::nested($data, WIDTH, HEIGHT);

/**
 * @var ImagePresenter $html
 */
$image = Treemap::image($data, WIDTH, HEIGHT, "png");

/**
 * @var CanvasPresenter $html
 */
$canvas = Treemap::canvas($data, WIDTH, HEIGHT);

echo $html->render($renderrer);
echo $nested->render($renderrer);
echo $canvas->render($renderrer);

header("Content-Type: image/png");
echo $image->render($renderrer);</code></pre>
            <h3 class="text-muted" id="canvas">Canvas</h3>
            <?= $canvas ?>

            <h3 class="text-muted" id="image">Image</h3>
            <p>
                <code class="language-php">ImagePresenter</code> outputs image in form of raw data. For this reason,
                pass appropriate content-type header with image data: <code class="language-php">header("Content-Type:
                    image/png")</code>.
            </p>
            // todo:

            <h3 class="text-muted" id="nested-maps">Nested maps</h3>
            <p>
                It is worth noting the <code class="language-php">NestedtmlPresenter</code>. In fact, it is a collection
                of treemaps with different detalization degree, which gives you conveniently navigate from one map to
                another via headers and breadcrumbs at top of presenter:
            </p>
            <?= $nested ?>

            <h2 id="contributing">License & contributing</h2>
            <p>
                <a target="_blank" href="https://github.com/codeagent/treemap">This project</a> is maintained by
                <a href="https://github.com/codeagent" target="_blank">codeagent</a>. If you find out any problems, please let me know:
                this is open source project on github, you can create a new issue or open a pull request.
            </p>
            <p>License: <strong>MIT</strong></p>
            <hr/>
            <p>
                Created by <a href="https://github.com/codeagent/t">codeagent</a>.
                &copy; <time><?= date("Y") ?></time>
            </p>
        </div>
        <div class="col-xs-3">
            <nav class="bs-docs-sidebar">
                <ul id="sidebar" class="nav nav-stacked fixed">
                    <li>
                        <a href="#description">Description</a>
                    </li>
                    <li>
                        <a href="#installation">Installation</a>
                    </li>
                    <li>
                        <a href="#basic-usage">Basic usage</a>
                    </li>

                    <li>
                        <a href="#node-rendering">Custom node rendering</a>
                    </li>

                    <li>
                        <a href="#map-formats">Map formats</a>
                        <ul class="nav nav-stacked">
                            <li><a href="#canvas">Canvas</a></li>
                            <li><a href="#image">Image</a></li>
                            <li><a href="#nested-maps">Nested maps</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#contributing">License & contributing</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $("body").scrollspy({target: ".bs-docs-sidebar"});
    $(document).on("scroll", function () {
        $(".bs-docs-sidebar").toggleClass("fixed top", $(window).scrollTop() > 370)
    });
</script>
</body>
</html>
