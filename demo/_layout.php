<?php
/**
 * @string $action
 * @srting $content
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="Treemap representation demo">
    <meta name="author" content="i.aleksey.yakovlev@gmail.com">

    <title>Treemap demo</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <link rel="stylesheet" href="../src/assets/treemap.css">
    <![endif]-->
    <style>
        body {
            padding-top: 80px;
        }

        .container {
            width: 1200px !important;;
        }
    </style>
</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Treemap</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li <?= $action == 'basic' ? "class='active'" : "" ?>><a href="index.php">Basic</a></li>
                <li <?= $action == 'nested' ? "class='active'" : "" ?>><a href="nested.php">Nested</a></li>
                <li <?= $action == 'canvas' ? "class='active'" : "" ?>><a href="canvas.php">Canvas</a></li>
                <li <?= $action == 'image' ? "class='active'" : "" ?>><a href="image.php">Image</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row" id="basic">
        <?= $content ?>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>

