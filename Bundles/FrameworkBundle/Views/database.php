<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="card col-md-12" style="margin-bottom: 10px;">
                        <div class="card-header">
                            <h4 class="card-title">Administration de la base de données</h4>
                            <p class="card-category"><b><?= $dbName ?></b> est le nom de la base de données</p>
                            <br>
                        </div>
                    </div>

                    <?php if ($databaseNotUpToDate) : ?>
                    <div class="alert alert-warning col-md-12" style="text-align: center;">
                        <i class="fa fa-exclamation"></i> La structure de votre base de données n'est pas à jour !
                        &nbsp; &nbsp;
                        <button class="btn btn-sm btn-danger" id="updateDatabaseTrigger">Mettre à jour</button>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="row" style="margin-bottom: 25px;">
                    <div class="col-md-12" style="text-align: center;">
                        <a href="<?= App::generateUrl('framework_admin_database_reset_route') ?>" class="btn btn-danger confirmRedirectionLinkSelector" data-confirmTitle="Voulez-vous vraiment réinitialiser la base de données ?">Réinitialiser la BDD</a> &nbsp;
<!--                        <button class="btn btn-warning">Supprimer la BDD</button> &nbsp;-->
                        <button class="btn btn-info" data-toggle="modal" data-target="#modifyDatabaseConnectionModal">Modifier les paramétres de connexion à la BDD</button> &nbsp;
                        <a href="<?= App::generateUrl('framework_admin_database_dump_route') ?>" class="btn btn-primary">Exporter le script de création de la BDD</a>
                    </div>
                </div>

                <div class="row">
                    <?= $schema ?>
                </div>
            </div>
        </div>
    </div>

<?= App::render('frameworkBundle:modals:database-modal') ?>
    <script>
        $('.card.schema ul li small u').click(function(e){
            let val = $(this).attr('data-target');

            $('.card.schema.selected-card').removeClass('selected-card');
            $(val).addClass('selected-card');
        });
    </script>
<?= App::render('frameworkBundle:includes:footer') ?>