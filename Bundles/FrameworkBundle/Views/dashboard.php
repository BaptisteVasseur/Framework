<?= App::render("frameworkBundle:includes:header") ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="content table-responsive table-full-width">
                        <div class="panel-1">
                            <div class="menu-item opacity0 item-1">
                                <a href="<?= App::generateUrl("framework_admin_bundle_route") ?>">
                                    <i class="fa fa-cubes"></i>
                                    <p>Bundles</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-6">
                                <a href="<?= App::generateUrl("framework_admin_controller_route") ?>">
                                    <i class="fa fa-gamepad"></i>
                                    <p>Controllers</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-3">
                                <a href="<?= App::generateUrl("framework_admin_entity_route") ?>">
                                    <i class="fa fa-users"></i>
                                    <p>Entités</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-4">
                                <a href="<?= App::generateUrl("framework_admin_form_route") ?>">
                                    <i class="fa fa-keyboard"></i>
                                    <p>Formulaires</p>
                                </a>
                            </div>

                            <div class="menu-item opacity0 item-2">
                                <a href="<?= App::generateUrl("framework_admin_config_route") ?>">
                                    <i class="fa fa-cogs"></i>
                                    <p>Configuration</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-9">
                                <a href="<?= App::generateUrl("framework_admin_route_route") ?>">
                                    <i class="fa fa-flag"></i>
                                    <p>Routes</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-7">
                                <a href="" id="divers-modal">
                                    <i class="fa fa-random"></i>
                                    <p>Générateurs (TODO)</p>
                                </a>
                            </div>

                            <div class="menu-item opacity0 item-8">
                                <a href="" id="planning-modal">
                                    <i class="fa fa-percent"></i>
                                    <p>Analyseur (TODO)</p>
                                </a>
                            </div>
                            <div class="menu-item opacity0 item-5">
                                <a href="<?= App::generateUrl("framework_admin_database_route") ?>" id="dressing-modal">
                                    <i class="fa fa-database"></i>
                                    <p>Base de données</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= App::render('frameworkBundle:includes:footer') ?>