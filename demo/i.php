<?php
include __DIR__ . "/../vendor/autoload.php";

use codeagent\treemap\Treemap;
use codeagent\treemap\demo\Utils;

const WIDTH  = 1200;
const HEIGHT = 800;

$data     = Utils::fake();
$gradient = Utils::gradient(0);
$min      = Utils::min($data, 'float');
$max      = Utils::max($data, 'float');

header("Content-Type: image/png");
echo Treemap::image($data, WIDTH, HEIGHT)->render(Utils::renderrer($gradient, $min, $max));
