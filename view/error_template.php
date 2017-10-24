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

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Fuelio Importer">
        
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#4b445c">
        <meta name="msapplication-TileImage" content="/mstile-144x144.png">
        <meta name="theme-color" content="#616161">

        <link href='//fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-amber.min.css" />
        <script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

        <style type="text/css">
            html,head,body { padding:0;margin:0; height: 100%}
            body {background:#F5F5F5;}
        </style>
    </head>

    <body>
        <div class="mdl-layout mdl-grid mdl-grid--no-spacing">
            <div class="mdl-cell mdl-cell--3-col-desktop mdl-cell--1-col-tablet"></div>
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
            if (ga !== undefined) {
                ga('send', 'exception', {
                    exDescription: <?= json_encode(get_class($ex)) ?>,
                    isFatal: true
                });
            }
        </script>
    </body>
</html>