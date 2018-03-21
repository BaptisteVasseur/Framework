<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des routes / url</h4>
                                <p class="card-category"><b>12 urls</b> disponibles</p>
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
                                        <input name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher une url / route">
                                    </div>
                                    <div class="col-md-5">
                                        <button class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddRouteStep1">Ajouter une url</a>
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
                                        <th>nom</th>
                                        <th>url</th>
                                        <th>params</th>
                                        <th style="display: none;">Action</th>
                                        <th style="display: none;">fichier</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($routes as $k => $route) : ?>
                                    <tr id="route-<?= $k ?>">
                                        <td class="dataRouteName"><?= $route['name'] ?></td>
                                        <td class="dataRouteUrl"><?= $route['route'] ?></td>
                                        <td class="dataRouteParams">
                                            <?php foreach ($route['params'] as $name => $param): ?>
                                                <span><b><?= $name ?></b> => <?= $param ?></span><br>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="dataRouteController" style="display: none;"><?= $route['controller'] ?>:<?= $route['action'] ?>()</td>
                                        <td class="dataRouteFile" style="display: none;"><?= $route['file'] ?></td>
                                        <td>
                                            <a href="" rel="tooltip" title="" class="btn btn-success btn-xs btn-view-route" data-idtr="#route-<?= $k ?>" data-original-title="Voir">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-primary btn-xs" data-original-title="Modifier">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-danger btn-xs" data-original-title="Supprimer">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
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
</div>


<?= App::render('frameworkBundle:modals:route-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>