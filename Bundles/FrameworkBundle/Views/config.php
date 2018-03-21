<?= App::render('frameworkBundle:includes:header') ?>
    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des paramétres de configurations</h4>
                                <p class="card-category"><b>9 paramétres</b> de configuration</p>
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
                                        <input type="text" name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher un paramétre">
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddConfig">Ajouter un paramétre</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body table-full-width row">

                                <table class="table table-hover" <?php if ($params2) : ?> style="width: 50% !important;" <?php endif; ?>>
                                    <thead>
                                    <tr>
                                        <th>nom</th>
                                        <th>valeur</th>
<!--                                        <th>fichier</th>-->
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($params1 as $paramName => $paramValue) : ?>
                                    <tr>
                                        <td><?= $paramName ?></td>
                                        <td><?= $paramValue ?></td>
<!--                                        <td>app/config</td>-->
                                        <td>
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
                                <?php if ($params2) : ?>
                                <table class="table table-hover col-md-6" style="width: 50% !important;">
                                    <thead>
                                    <tr>
                                        <th>nom</th>
                                        <th>valeur</th>
<!--                                        <th>fichier</th>-->
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($params2 as $paramName => $paramValue) : ?>
                                        <tr>
                                            <td><?= $paramName ?></td>
                                            <td><?= $paramValue ?></td>
<!--                                            <td>app/config</td>-->
                                            <td>
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
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?= App::render('frameworkBundle:modals:config-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>