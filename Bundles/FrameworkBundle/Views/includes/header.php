<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Symplify</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <?= App::getRessource('frameworkBundle:css:bootstrap.min.css') ?>
    <?= App::getRessource('frameworkBundle:css:animate.min.css') ?>
    <?= App::getRessource('frameworkBundle:css:light-bootstrap-dashboard.css') ?>
    <?= App::getRessource('frameworkBundle:css:style.css') ?>
    <?= App::getRessource('frameworkBundle:css:pe-icon-7-stroke.css') ?>
    <?= App::getRessource('frameworkBundle:css:fontawesome.css') ?>

    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

    <?= App::getRessource('frameworkBundle:js:jquery.js') ?>

</head>
<body>

<div class="wrapper">

    <?= App::renderController("frameworkBundle:frameworkController@sidebar") ?>