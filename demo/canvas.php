<?php
include __DIR__ . "/../vendor/autoload.php";

use codeagent\treemap\Treemap;
use codeagent\treemap\demo\Utils;

const WIDTH    = 1200;
const HEIGHT   = 800;

$action   = 'canvas';
$data     = Utils::fake();
$gradient = Utils::gradient(0);
$min      = Utils::min($data, 'float');
$max      = Utils::max($data, 'float');

//
$canvas = Treemap::canvas($data, WIDTH, HEIGHT)->render(Utils::renderrer($gradient, $min, $max));

ob_start(); ?>
    <div class="row">
        <div class="col-xs-12">
            <h2>Canvas</h2>
            <?= $canvas ?>
        </div>
    </div>

<?php $content = ob_get_clean();

include_once "_layout.php";
