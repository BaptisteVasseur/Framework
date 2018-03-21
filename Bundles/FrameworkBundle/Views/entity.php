<?= App::render('frameworkBundle:includes:header') ?>

    <div class="main-panel">

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Administration des entités</h4>
                                <?php
                                    foreach ($allBundleEntities as $bundleName => $bundleEntities) :
                                        foreach ($bundleEntities as $entity) :
                                            $entityNB++;
                                        endforeach;
                                    endforeach; ?>
                                <p class="card-category"><b><?= $entityNB ?> entitées</b> repartis en <?= count($bundles) ?> bundles</p>
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
                                        <input type="text" name="collaborator" id="input" class="form-control input-search-collaborator" value="" placeholder="Rechercher une entité">
                                    </div>
                                    <div class="col-md-5">
                                        <button class="btn btn-primary">Rechercher</button>
                                    </div>

                                    <div class="col-md-4" style="text-align: right; padding-right: 30px;">
                                        <a href="" class="btn btn-success" data-toggle="modal" data-target="#modalAddEntityStep1">Créer une entité</a>
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
                                        <th>Nombres de propriétés</th>
                                        <th>Nombres de relations</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($allBundleEntities as $bundleName => $bundleEntities) : ?>
                                        <?php foreach ($bundleEntities as $entity) : ?>
                                    <tr>
                                        <td><?= $bundleName ?>:<?= $entity['name'] ?></td>
                                        <td><b><?= str_replace('Entity', '', $entity['name']) ?></b></td>
                                        <td><?= str_replace('Bundle', '', $bundleName) ?></td>
                                        <td><?= count($entity['properties']) ?> propriétés</td>
                                        <td><?= $entity['nbOneToOne'] + $entity['nbOneToMany'] ?> relations (OneToOne : <?= $entity['nbOneToOne'] ?>, OneToMany : <?= $entity['nbOneToMany'] ?>)</td>
                                        <td>
<!--                                            <a href="" rel="tooltip" title="" class="btn btn-primary btn-xs" data-original-title="Modifier">-->
<!--                                                <i class="fa fa-pencil-alt"></i>-->
<!--                                            </a>-->
                                            <a href="<?= App::generateUrl('framework_admin_form_entity_remove_route', ['bundleName' => $bundleName , 'entityName' => $entity['name'] ]) ?>" class="btn btn-danger btn-xs deleteTrigger">
                                                <i class="fa fa-times"></i>
                                            </a>
<!--                                            <a href="" rel="tooltip" title="" class="btn btn-success btn-xs" data-original-title="Désactiver">-->
<!--                                                <i class="fa fa-lock"></i>-->
<!--                                            </a>-->
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
            if(confirm("Voulez-vous vraiment supprimer l'entité ? ")){
                window.location.href = $(this).attr('href');
            }
        })
    </script>

<?= App::render('frameworkBundle:modals:entity-modal') ?>
<?= App::render('frameworkBundle:includes:footer') ?>