<?php
    use Core\Utils\Utils;
?>

<div class="sidebar" data-color="red" data-image="<?= App::getRessource("frameworkBundle:img:sidebar-4.jpg") ?>" style="background-image: url(<?= App::getRessource("frameworkBundle:img:sidebar-4.jpg") ?>);">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="<?= BASE_URL ?>" class="simple-text">
                Administration
            </a>
        </div>

        <ul class="nav">
            <li class="active">
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_dashboard_route") ?>">
                    <i class="pe-7s-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <br>
            <li <?= Utils::setActivePageInMenu("framework_admin_bundle_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_bundle_route") ?>">
                    <i class="pe-7s-plugin"></i>
                    <p>Bundles</p>
                </a>
            </li>
            <li <?= Utils::setActivePageInMenu("framework_admin_controller_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_controller_route") ?>">
                    <i class="pe-7s-joy"></i>
                    <p>Controllers</p>
                </a>
            </li>
            <li <?= Utils::setActivePageInMenu("framework_admin_entity_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_entity_route") ?>">
                    <i class="pe-7s-users"></i>
                    <p>Entités</p>
                </a>
            </li>
            <li <?= Utils::setActivePageInMenu("framework_admin_form_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_form_route") ?>">
                    <i class="pe-7s-display2"></i>
                    <p>Formulaires (TODO)</p>
                </a>
            </li>

            <li <?= Utils::setActivePageInMenu("framework_admin_config_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_config_route") ?>">
                    <i class="pe-7s-config"></i>
                    <p>Configuration</p>
                </a>
            </li>

            <li <?= Utils::setActivePageInMenu("framework_admin_route_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl("framework_admin_route_route") ?>">
                    <i class="pe-7s-global"></i>
                    <p>Routes</p>
                </a>
            </li>

            <li <?= Utils::setActivePageInMenu("") ?> >
                <a class="nav-link" href="">
                    <i class="pe-7s-shuffle"></i>
                    <p>Générateurs (TODO)</p>
                </a>
            </li>

            <li <?= Utils::setActivePageInMenu("") ?> >
                <a class="nav-link" href="">
                    <i class="pe-7s-search"></i>
                    <p>Analyseur (TODO)</p>
                </a>
            </li>

            <li <?= Utils::setActivePageInMenu("framework_admin_database_route") ?> >
                <a class="nav-link" href="<?= App::generateUrl('framework_admin_database_route') ?>">
                    <i class="pe-7s-server"></i>
                    <p>Base de données <?php if ($databaseNotUpToDate) : ?><div class="badge badge-warning" style="font-size: 13px; color: white; margin-left: 10px; background-color: #ffb100;">!</div><?php endif; ?></p>
                </a>
            </li>

            <li class="nav-item active active-pro">
                <a class="nav-link active" href="">
                    <i class="pe-7s-refresh-2"></i>
                    <p>Créer un projet  (TODO)</p>
                </a>
            </li>
        </ul>
    </div>
</div>