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

    <link href='//fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en&amp;display=swap' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-amber.min.css" />
    <script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript">
        window.cookieconsent_options = {
            "message": "This website uses cookies to ensure you get the best experience on our website",
            "dismiss": "Got it!",
            "learnMore": "More info",
            "link": null,
            "theme": "dark-bottom"
        };
    </script>

    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js" async></script>
    <!-- End Cookie Consent plugin -->

    <?php
    /** @var \FuelioImporter\IConverter $converter */
    foreach ($provider as $converter) {
        if (!empty($converter->getStylesheetLocation())) {
            ?>
            <link rel="stylesheet" href="<?= $converter->getStylesheetLocation() ?>">
            <?php
        }
    }
    ?>

    <style>
        body {
            background: #F5F5F5;
        }

        .ghost {
            display: none;
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

        #converters .mdl-card {
            cursor: pointer;
        }

        .fullwidth {
            width: 100%;
        }
    </style>
</head>

<body>
<form action="convert.php" method="post" enctype="multipart/form-data" class="ghost">
    <fieldset>
        <input type="text" name="n" required="required" placeholder="Car name"/>
        <input type="file" name="f"/>
        <?php foreach ($provider as $converter) : ?>
            <input type="radio" id="radio-<?= $converter->getName() ?>" name="c" value="<?= $converter->getName() ?>"
                   required="required"/> <label
                for="radio-<?= $converter->getName() ?>"> <?= $converter->getTitle() ?></label>
            <?php $form = $converter->getCard()->getForm(); if ($form) : ?>
                <fieldset><?php foreach ($form as $field) : echo $field->render(); endforeach; ?></fieldset>
                <?php endif; // $form ?>
        <?php endforeach; ?>
        <textarea name="datastream" id="datastream"></textarea>
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
                <a class="mdl-navigation__link" href="https://www.programistyk.pl"><img
                        src="https://www.programistyk.pl/favicon-32x32.png" width="16" height="16" alt=""/> Programistyk</a>
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
        <nav class="mdl-navigation">
        <a class="mdl-navigation__link" href="https://github.com/Programistyk/FuelioImport">GitHub</a>
        <a class="mdl-navigation__link" href="https://www.programistyk.pl">Programistyk</a>
        </nav>
    </div>
    <main class="mdl-layout__content">
        <div class="page-content mdl-grid">
            <div
                class="mdl-cell mdl-cell--2-offset-desktop mdl-cell--1-offset-tablet mdl-cell--1-offset-phone mdl-cell--8-col-desktop mdl-cell--7-col-tablet mdl-cell--3-col-phone mdl-card mdl-shadow--2dp demo-card-wide">
                <div class="mdl-card__title">
                    <h2 class="mdl-card__title-text">Welcome</h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <p>Welcome to Fuelio Import Converter, an unofficial tool to ease data migration process.</p>
                    <p>Every card below represents a backup-format converter. You can drop your backup file onto it, or
                        use standard file-open dialog by clicking on chosen card.</p>
                    <p>If you need more support, hit an error or want to join out team, please use button below to get
                        into our Wiki.
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"
                       href="https://github.com/Programistyk/FuelioImport/wiki/Getting-started">
                        Get Started
                    </a>

                </div>
            </div>
        </div>

        <!-- Google ads placeholder -->

        <div class="page-content mdl-grid" id="converters">
            <?php
            foreach ($provider as $cid => $converter) {
                $card = $converter->getCard();
                ?>
                <div
                    class="mdl-cell--stretch mdl-cell mdl-cell--6-col-desktop mdl-cell--11-col-tablet mdl-cell--1-offset-tablet mdl-cell--1-offset-phone mdl-cell--11-col-phone">
                    <div class="mdl-card fullwidth mdl-shadow--2dp mdl-cell--stretch  <?= $card->getClass() ?>"
                         id="prov-<?= $converter->getName() ?>" data-name="<?= $converter->getName() ?>">
                        <div class="mdl-card__title mdl-card--border">
                            <h2 class="mdl-card__title-text"><?= $card->getTitle() ?></h2>
                        </div>
                        <div class="mdl-card__supporting-text ">
                            <?= $card->getSupporting() ?>
                            <?php $form = $card->getForm(); if ($form) :
                    ?>
                    <fieldset><?php foreach ($form as $field) : echo $field->render(); endforeach; //$form->fields ?></fieldset>
                    <?php endif; //$form ?>
                        </div>

                        <div class="mdl-card__actions mdl-card--border">
                            <a id="select-file-<?= $converter->getName() ?>"
                               class="mdl-button sf mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-button--colored mdl--button--primary"><i
                                    class="material-icons">file_upload</i></a>
                            <?php foreach ($card->getActions() as $action) { ?>
                                <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"
                                   href="<?= $action[2] ?>">
                                    <?= $action[0] ?>
                                </a>
                            <?php } ?>
                        </div>
                        <?php if (!empty($card->getMenu())) { ?>
                            <div class="mdl-card__menu">
                                <?php foreach ($card->getMenu() as $menu) { ?>
                                    <button
                                        class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                                        <i class="material-icons">share</i>
                                    </button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="mdl-tooltip" for="select-file-<?= $converter->getName() ?>">Select file to
                        convert
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- Let's make some space for tooltips -->
        <div class="mdl-grid"></div>
    </main>
</div>
<?php @include '../view/analytics.html' ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    window.onload = function () {
        var filereaderload = function () {
            if (this.error) {
                alert(this.error);
                return false;
            }
            if ("" === this.result || this.result.indexOf(",") === -1) {
                alert("Something went wrong, FileReader API returned no file content. Please use desktop browser, or file an issue report.");
                return false;
            }
            $("#datastream").val(this.result);
            $("form.ghost").attr("action", "convert.php?converter=" + $("form input[name=c]").val());//.submit();
        };

        var dragenter = function (e) {
            e.preventDefault();
            return false;
        };

        var dragleave = function (e) {
            e.preventDefault();
            $(this).removeClass("dropactive");
            return false;
        };

        var dragover = function (e) {
            e.preventDefault();
            $(this).addClass("dropactive");
            return false;
        };

        var process_fields = function(form, target) {
            $(".mdl-layout__container fieldset :input").each(function(i, e) {
                var el = $(e);
                $("form.ghost [name='" + el.attr("name") + "']").val(el.val());
            });
        };

        var drop = function (e) {
            e.preventDefault();
            $(this).removeClass("dropactive").addClass("working");
            if (!FileReader)
                alert("FileReader interface is not available. Upgrade your browser or click this card to select file :)");
            else {
                var form = $("form.ghost");
                var name = $(this).data("name");
                form.find("input[name=c]").val([name]);
                process_fields();
                var fr = new FileReader();
                fr.onloadend = filereaderload;
                fr.onerror = function(e) {
                    console.error(e);
                    alert("Upload failed due to FileReader API error: " + e.message || "Unknown error");
                    return false;
                };
                if (!e.originalEvent || !e.originalEvent.dataTransfer || !e.originalEvent.dataTransfer.files || e.originalEvent.dataTransfer.files.length === 0) {
                    console.debug(e.originalEvent);
                    alert("Unknown error occured at FileReader API, please use desktop browser and upload via form.");
                }

                fr.readAsDataURL(e.originalEvent.dataTransfer.files[0]);
            }
            return false;
        };

        var click = function (e) {
            // Prevent action menu items from triggering file selection dialog, same for form fields
            var etgt = $(e.target);
            var form = $("form.ghost");
            process_fields();

            if (etgt.is(".mdl-button")
                || (etgt.parent().is(".mdl-button") && etgt.parent().attr("href") !== undefined)
                || $(etgt).parents("fieldset").length>0)
                return;
            e.preventDefault();
            // Set form's "C" value
            var name = $(this).closest(".mdl-card[id]").data("name");
            form.find("input[name=c]").val([name]);
            form.find(":file").click();
            return false;
        };

        if (window.FileReader) {
            var conv = $("#converters");
            conv.on({
                "dragenter": dragenter,
                "dragleave": dragleave,
                "dragover": dragover,
                "drop": drop,
                "click": click
            }, ".mdl-card");
            conv.find("a.sf").on("click", click);
            $("form.ghost :file").change(function () {
                $("form.ghost").attr("action", "convert.php?converter=" + $("form input[name=c]").val()).submit().get(0).reset();

            });
        }
    }
</script>
</body>
</html>