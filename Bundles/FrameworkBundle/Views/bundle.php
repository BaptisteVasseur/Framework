<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des bundles</h4>
                                <p class="card-category"><b><?= count($bundles) ?> bundles</b> associés au projet</p>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="">
                            <div style="padding: 10px;">
                                <form action="" class="row">
                                    <div class="col-md-3">
                                        <input name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher un bundle">
                                    </div>
                                    <div class="col-md-5">
                                        <button class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddBundle">Créer un bundle</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php foreach ($bundles as $bundle) : ?>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <a class="card-delete deleteTrigger" href="<?= App::generateUrl('framework_admin_form_bundle_remove_route', ['bundleName' => $bundle['name']]) ?>">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <h5 class="card-title"><i class="fa fa-cubes"></i></h5>
                                    <p class="card-text"><?= $bundle['name'] ?></p>
                                    <p class="card-text"><small class="text-muted">Créer le <?= Core\Utils\Utils::format_date($bundle['date'], 2) ?> par <a href="<?= $bundle['link'] ?>"><b><?= $bundle['author'] ?></b></a></small></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.deleteTrigger').click(function(e){
            e.preventDefault();
            if(confirm("Voulez-vous vraiment supprimer le bundle ? ")){
                window.location.href = $(this).attr('href');
            }
        });
    </script>

<?= App::render('frameworkBundle:modals:bundle-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>