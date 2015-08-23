<!doctype html >
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="A backup file converter for the great Fuelio App">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fuelio Import Converter</title>

        <!-- Add to homescreen for Chrome on Android -->
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">

        <!-- Add to homescreen for Safari on iOS -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Material Design Lite">
        <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

        <!-- Tile icon for Win8 (144x144 + tile color) -->
        <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
        <meta name="msapplication-TileColor" content="#3372DF">

        <link href='//fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.2/material.blue_grey-amber.min.css" />
        <script src="https://storage.googleapis.com/code.getmdl.io/1.0.0/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

        <style type="text/css">
            html,head,body { padding:0;margin:0; height: 100%}
            body {background:#F5F5F5;}
        </style>
    </head>

    <body>
        <div class="mdl-layout mdl-grid mdl-grid--no-spacing">
            <div class="mdl-cell mdl-cell--3-col"></div>
            <div class="mdl-card mdl-shadow--2dp mdl-cell mdl-cell--6-col mdl-cell--middle">
                <div class="mdl-card__title">
                    <h2 class="mdl-card__title-text" id="error-class">Whoops! An error occured</h2>
                    <span class="mdl-tooltip mdl-tooltip--large" for="error-class">Error of class <?= get_class($ex) ?></span>
                </div>
                <div class="mdl-card__supporting-text">
                    <h4><?= $ex->getMessage() ?></h4>
                    <?php if (defined('DEBUG')) { ?>
                    <code><?= $ex->getTraceAsString()?></code>
                    <?php }// DEBUG?>
                </div>
                <div class="mdl-card__supporting-text">
                    If you need further assistance, visit project's GitHub page: <a href="https://github.com/Programistyk/FuelioImport" class="mdl-button mdl-js-button mdl-button--primary">FuelioImport</a>
                </div>
                <div class="mdl-card__actions">
                    <a class="mdl-button mdl-button--primary mdl-js-button mdl-button--raised mdl-js-ripple-effect" href="/">Go back</a>
                </div>
            </div>
        </div>
        <?php @include 'analytics.html' ?>
        <script>
            if (ga != undefined) {
                ga('send', 'exception', {
                    exDescription: <?= json_encode(get_class($ex)) ?>,
                    isFatal: true
                });
            }
        </script>
    </body>
</html>