<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des formulaires</h4>
                                <p class="card-category"><b>13 formulaires</b> repartis en 23 bundles</p>
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
                                        <input name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher un fomulaire">
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddFormStep1">Créer un formulaire</a>
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

                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Identifiant</th>
                                        <th>Nom</th>
                                        <th>Fichier</th>
                                        <th>Bundle</th>
                                        <th>Nombres de champs</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="userList">
                                    <tr>
                                        <td>userBundle:userForm:login</td>
                                        <td>Login</td>
                                        <td>userForm</td>
                                        <td>user</td>
                                        <td>12 champs</td>
                                        <td>
                                            <a href="" rel="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Modifier">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-primary btn-xs" data-original-title="Modifier">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-danger btn-xs" data-original-title="Supprimer">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-success btn-xs" data-original-title="Désactiver">
                                                <i class="fa fa-lock"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>appBundle:formationForm:add</td>
                                        <td>Add</td>
                                        <td>formationForm</td>
                                        <td>app</td>
                                        <td>4 champs</td>
                                        <td>
                                            <a href="" rel="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Modifier">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-primary btn-xs" data-original-title="Modifier">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-danger btn-xs" data-original-title="Supprimer">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            <a href="" rel="tooltip" title="" class="btn btn-success btn-xs" data-original-title="Désactiver">
                                                <i class="fa fa-lock"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= App::render('frameworkBundle:modals:form-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>