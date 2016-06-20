<?php
include __DIR__ . "/../vendor/autoload.php";

use codeagent\treemap\Treemap;
use codeagent\treemap\demo\Utils;
use codeagent\treemap\presenter\NestedHtmlPresenter;

const WIDTH    = 1200;
const HEIGHT   = 800;

$action   = 'nested';
$data     = Utils::filesystem();
$gradient = Utils::gradient(0);

$treemap                    = new Treemap($data, WIDTH, HEIGHT);
$treemap->valueAttribute    = 'size';
$treemap->childrenAttribute = 'files';
//
$nested = (new NestedHtmlPresenter($treemap))->render();

ob_start(); ?>
    <div class="row">
        <div class="col-xs-12">
            <h2>Filesystem</h2>
            <?= $nested ?>
        </div>
    </div>

<?php $content = ob_get_clean();

include_once "_layout.php";
