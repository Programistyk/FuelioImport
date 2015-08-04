<?php
require_once 'bootstrap.php';

$provider = new FuelioImporter\ConverterProvider();
?>
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
        <?php
        foreach ($provider as $converter) {
            if (!empty($converter->getStylesheetLocation())) {
                ?>
                <link rel="stylesheet" href="<?= $converter->getStylesheetLocation() ?>">
                <?php
            }
        }
        ?>

        <style>
            body {background:#F5F5F5;}
            .ghost {
                display:none;
            }
            .demo-card-wide > .mdl-card__title {
                color: #fff;
                height: 176px;
                /* source: http://mrg.bz/3dzwgF */
                background: url('card_background.jpg') center / cover;
            }
            .demo-card-wide > .mdl-card__menu {
                color: #fff;
            }
            .dropactive {
                background-color: #F9F9F9;
                outline: dashed 2px gray;
                outline-offset: 8px;
            }
        </style>
    </head>

    <body>
        <form action="convert.php" method="post" enctype="multipart/form-data" class="ghost">
            <fieldset>
                <input type="text" name="n" required="required" placeholder="Car name"/>
                <input type="file" name="f" required="required"/>
                <?php foreach ($provider as $converter) { ?>
                    <input type="radio" id="radio-<?= $converter->getName() ?>" name="c" value="<?= $converter->getName() ?>" required="required"/> <label for="radio-<?= $converter->getName() ?>"> <?= $converter->getTitle() ?></label>
                <?php } ?>
                <input type="submit" value="Process"/>
            </fieldset>
        </form>
        <div class="mdl-layout mdl-js-layout mdl-layout--overlay-drawer-button">
            <header class="mdl-layout__header mdl-layout__header--scroll">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title">Fuelio Import Converter</span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation -->
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="https://github.com/Programistyk/FuelioImport">GitHub</a>
                        <a class="mdl-navigation__link" href="http://www.programistyk.pl">Programistyk</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title">Converter</span>
                <nav class="mdl-navigation">
                    <?php
                    foreach ($provider as $converter) {
                        ?>
                        <a class="mdl-navigation__link" href="#prov-<?= $converter->getName() ?>"><?= $converter->getTitle() ?>
                            <?php
                        }
                        ?>
                </nav>
                <nav class="mdl-navigation"
                     <a class="mdl-navigation__link" href="https://github.com/Programistyk/FuelioImport">GitHub</a>
                    <a class="mdl-navigation__link" href="http://www.programistyk.pl">Programistyk</a>
                </nav>
            </div>
            <main class="mdl-layout__content">
                <div class="page-content mdl-grid">
                    <div class="mdl-cell mdl-cell--2-col"></div>
                    <div class="mdl-card mdl-shadow--2dp demo-card-wide mdl-cell mdl-cell--8-col">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Welcome</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Welcome to Fuelio Import Converter, an unofficial tool to ease data migration process. Drop your file to proper card to begin.
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="https://github.com/Programistyk/FuelioImport/wiki/Getting-started">
                                Get Started
                            </a>

                        </div>
                        <div class="mdl-card__menu">
                            <!--                            <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                                                            <i class="material-icons">share</i>
                                                        </button>-->
                        </div>
                    </div>
                </div>

                <!-- Google ads placeholder -->

                <div class="page-content mdl-grid" id="converters">
                    <div class="mdl-cell mdl-cell--1-col"></div>

                    <?php
                    foreach ($provider as $converter) {
                        $card = $converter->getCard();
                        ?>
                        <div class="mdl-card mdl-shadow--2dp mdl-cell mdl-cell--5-col <?= $card->getClass() ?>" id="prov-<?= $converter->getName() ?>">
                            <div class="mdl-card__title">
                                <h2 class="mdl-card__title-text"><?= $card->getTitle() ?></h2>
                            </div>
                            <div class="mdl-card__supporting-text">
                                <?= $card->getSupporting() ?>
                            </div>
                            <?php if (!empty($card->getActions())) { ?>
                                <div class="mdl-card__actions mdl-card--border">
                                    <?php foreach ($card->getActions() as $action) { ?>
                                        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="<?= $action[2] ?>">
                                            <?= $action[0] ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($card->getMenu())) { ?>
                                <div class="mdl-card__menu">
                                    <?php foreach ($card->getMenu() as $menu) { ?>
                                        <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                                            <i class="material-icons">share</i>
                                        </button>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="mdl-cell mdl-cell--1-col"></div>
                </div>
            </main>
        </div>
        <?php @include '../view/analytics.html' ?>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        window.onload = function () {
            var dragenter = function (e) {
                e.preventDefault();
                return false;
            }

            var dragleave = function (e) {
                e.preventDefault();
                $(this).removeClass("dropactive");
                return false;
            }

            var dragover = function (e) {
                e.preventDefault();
                $(this).addClass("dropactive");
                return false;
            }

            var drop = function (e) {
                e.preventDefault();
                $(this).removeClass("dropactive");
                return false;
            }

            if (window.FileReader)
            {
                $("#converters").on({
                    "dragenter": dragenter,
                    "dragleave": dragleave,
                    "dragover": dragover,
                    "drop": drop
                }, ".mdl-card");
            }
        }
    </script>
</body>
</html>