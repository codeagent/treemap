<?php
include __DIR__ . "/../vendor/autoload.php";

use codeagent\treemap\Treemap;
use codeagent\treemap\presenter\HtmlPresenter;
use codeagent\treemap\demo\Utils;
use codeagent\treemap\presenter\NodeInfo;
use codeagent\treemap\presenter\NodeContent;

const WIDTH    = 600;
const HEIGHT   = 450;

$action    = 'basic';
$data      = Utils::fake();
$gradient  = Utils::gradient(0);
$min       = Utils::min($data, 'float');
$max       = Utils::max($data, 'float');
$renderrer = function (NodeInfo $node) use ($gradient, $min, $max) {
    $data = $node->data();
    $node->background($gradient->color(($data['float'] - $min) / ($max - $min)));
};

//
$default = Treemap::html($data, WIDTH, HEIGHT)->render();

//
$background = Treemap::html($data, WIDTH, HEIGHT)->render($renderrer);

//
$frameTM = new Treemap($data, WIDTH, HEIGHT);
$frameTM->setNodeFrameResolver(Utils::frame());
$frame = (new HtmlPresenter($frameTM))->render($renderrer);

//
$content = Treemap::html($data, WIDTH, HEIGHT)->render(function (NodeInfo $node) use ($gradient, $min, $max) {
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
});

ob_start(); ?>
    <div class="row">
        <div class="col-xs-6">
            <h2>Default</h2>
            <?= $default ?>
        </div>
        <div class="col-xs-6">
            <h2>Background</h2>
            <?= $background ?>
        </div>
        <div class="col-xs-6">
            <h2>Custom frame</h2>
            <?= $frame ?>
        </div>
        <div class="col-xs-6">
            <h2>Node content</h2>
            <?= $content ?>
        </div>
    </div>


<?php $content = ob_get_clean();

include_once "_layout.php";
