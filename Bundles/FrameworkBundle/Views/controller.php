<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des controllers</h4>
                                <p class="card-category"><b>40 controllers</b> repartis en 12 bundles</p>
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
                                        <input type="text" name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher un controller">
                                    </div>
                                    <div class="col-md-5">
                                        <button class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddController">Créer un controller</a>
<!--                                        <a href="" class="btn btn-info">Créer une relation</a>-->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body table-full-width table-responsive">

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>identifiant</th>
                                        <th>Nom</th>
                                        <th>bundle</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($bundleControllers as $bundleName => $controllers) : ?>
                                        <?php foreach ($controllers as $controller) : ?>
                                            <tr>
                                                <td><?= $bundleName ?>/<?= $controller ?></td>
                                                <td><b><?= $controller ?></b></td>
                                                <td><?= $bundleName ?></td>
                                                <td>
<!--                                                    <a href="" rel="tooltip" title="" class="btn btn-primary btn-xs" data-original-title="Modifier">-->
<!--                                                        <i class="fa fa-pencil-alt"></i>-->
<!--                                                    </a>-->
                                                    <a href="<?= App::generateUrl('framework_admin_form_controller_remove_route', ['bundleName' => $bundleName , 'controllerName' => $controller ]) ?>" class="btn btn-danger btn-xs deleteTrigger">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.deleteTrigger').click(function(e){
            e.preventDefault();
            if(confirm("Voulez-vous vraiment supprimer le controller ? ")){
                window.location.href = $(this).attr('href');
            }
        })
    </script>

<?= App::render('frameworkBundle:modals:controller-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>