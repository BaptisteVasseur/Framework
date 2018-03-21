<form action="<?= App::generateUrl("framework_admin_form_route_route") ?>" method="post">
    <!-- Modal -->
    <div class="modal fade" id="modalAddRouteStep1" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout d'une nouvelle route</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputNom">Nom de la route</label>
                            <input class="form-control" id="inputNom" name="inputNom" placeholder="login_route">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputSaveBundleName">Enregister dans le fichier "routes" du bundle :</label>
                            <select name="inputSaveBundleName" class="form-control" id="inputSaveBundleName">
                                <option selected>Choisir...</option>
<!--                                <option value="">app</option>-->
                                <?php foreach ($bundles as $bundle) : ?>
                                    <option value="<?= $bundle ?>"><?= $bundle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="inputUrl">Url</label>
                        <input type="text" class="form-control" id="inputUrl" name="inputUrl" placeholder="Url de la route (paramétre ? : {:nomParam} )">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputBundle">Bundle</label>
                            <select name="inputBundle" class="form-control" id="inputBundle" >
                                <option selected>Choisir...</option>
                                <?php foreach ($bundles as $bundle) : ?>
                                    <option value="<?= $bundle ?>"><?= $bundle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="ControllerList">
                            <label for="inputController">Controller</label>
                            <select name="inputController" class="form-control" id="inputController">
                                <option selected>Choisir...</option>
                                <?php foreach ($controllers as $controller) : ?>
                                    <option value="<?= $controller ?>"><?= $controller ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="ActionList">
                            <label for="inputAction">Action</label>
                            <select name="inputAction" class="form-control" id="inputAction">
                                <option selected>Choisir...</option>
                                <?php foreach ($actions as $action) : ?>
                                    <option value="<?= $action ?>"><?= $action ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <br>
                    <button class="btn btn-info btn-sm" id="btn-add-bundle">Créer un Bundle</button>
                    <button class="btn btn-info btn-sm" style="display: none" id="btn-add-controller">Créer un Controller</button>
                    <button class="btn btn-info btn-sm" style="display: none" id="btn-add-action">Créer une Action</button>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Fermer</button>
                    <button type="button" class="btn btn-primary btn-sm" id="nextRouteModal">Suivant <i class="fa fa-arrow-alt-circle-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAddRouteStep2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout d'une nouvelle route</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="mt-0 mb-1">Paramétre de la route</h4>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>nom</th>
                                <th>type</th>
                                <th>regex</th>
                            </tr>
                            </thead>
                            <tbody id="route_param_list">
                            <tr style="display: none;" id="tr-example">
                                <td>__IDENTIFIER__</td>
                                <td>__NAME__</td>
                                <td>
                                    <select class="form-control select-route-param-type" data-changeValue="#inputRegex-__NAME__">
                                        <option selected>Choisir...</option>
                                        <option value="([0-9])">Chiffre : ([0-9])</option>
                                        <option value="([0-9]+)">Nombre : ([0-9]+)</option>
                                        <option value="([a-zA-Z]+)">Lettre : ([a-zA-Z]+)</option>
                                        <option value="([a-zA-Z0-9]+)">Chaine : ([a-zA-Z0-9]+)</option>
                                        <option value="(.+)">Tout : (.+)</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="inputRegex-__NAME__" name="inputParams[__NAME__]" class="form-control" placeholder="Regex">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button class="btn btn-primary btn-sm" style="margin-right: 10px;" id="previousRouteModal"><i class="fa fa-arrow-alt-circle-left"></i> Précédent</button>
                    <button class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button class="btn btn-success btn-sm" id="sendForm">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalViewRoute" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aperçu de la route : <span id="titleRouteName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p><b>Nom :</b> <span id="modalViewRouteName"></span> </p>
                    <p><b>Route :</b> <span id="modalViewRouteUrl"></span> </p>
                    <p><b>Params :</b> <span id="modalViewRouteParams"></span> </p>
                    <p><b>Controller :</b> <span id="modalViewRouteController"></span> </p>
                    <p><b>File :</b> <span id="modalViewRouteFile"></span> </p>

                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button class="btn btn-danger btn-sm" data-dismiss="modal" style="">Fermer</button>
                </div>
            </div>
        </div>
    </div>

</form>

<script>

    $(document).on('click', ".btn-view-route", function(e){

        e.preventDefault();
        let identifier = $(this).attr('data-idtr');

        $('#titleRouteName').html( $(identifier + ' .dataRouteName').html() );

        $('#modalViewRouteName').html( $(identifier + ' .dataRouteName').html() );
        $('#modalViewRouteUrl').html( $(identifier + ' .dataRouteUrl').html() );
        $('#modalViewRouteParams').html( $(identifier + ' .dataRouteParams').html() );
        $('#modalViewRouteController').html( $(identifier + ' .dataRouteController').html() );
        $('#modalViewRouteFile').html( $(identifier + ' .dataRouteFile').html() );


       $('#modalViewRoute').modal('show');
    });

    $(document).on('change', '.select-route-param-type', function(){
       let val = $(this).val();
       if(val === "custom"){ val = ""; }
            let input = $(this).attr('data-changeValue');
            $(input).val(val);
    });

    // Lorsque l'on clique sur le bouton suivant
    $('#nextRouteModal').click(function(e){
        e.preventDefault();

        $('#route_param_list').html( '<tr style="display: none;" id="tr-example">' + $('#tr-example').html() + '</tr>' );
        let val = $('#inputUrl').val();
        let matches = val.match(/\{\:[a-zA-Z0-9\-_]+\}/g);

        if(matches !== null){
            matches.forEach(function(shortcut){
                let clone = $('#tr-example').html();

                let name = shortcut.replace(/\{\:/, '').replace(/\}/, '');
                console.log(name);
                clone = clone.replace(/__IDENTIFIER__/g, shortcut);
                clone = clone.replace(/__NAME__/g, name);

                $('#route_param_list').append("<tr>" + clone + "</tr>");
            });
        }else{
            $('#sendForm').trigger('click');
        }

        $('#modalAddRouteStep1').modal('hide');
        $('#modalAddRouteStep2').modal('show');
    });

    // Lorsque l'on clique sur le bouton précédent
    $('#previousRouteModal').click(function(e){
        e.preventDefault();
        $('#modalAddRouteStep2').modal('hide');
        $('#modalAddRouteStep1').modal('show');
    });

    // Récupéres les bundles
    let loadBundles = function() {
        $.get('<?= App::generateUrl("framework_admin_form_route_getBundleList_route") ?>', {}, function(donnees){
            $('#inputBundle').html(donnees);
        });
    };

    // Lorsque l'on change le champs pour choisir un bundle
    $('#inputBundle').change(function(e){
        var bundleName = $(this).val();
        $.get('<?= App::generateUrl("framework_admin_form_route_getControllerList_route") ?>', {bundleName: bundleName}, function(donnees){
            $('#inputController').html(donnees);
            $('#btn-add-controller').show(); // Quand on selectionne un bundle, on affiche le bouton pour créer un controller et on cache le bouton pour créer une action
            $('#btn-add-action').hide();
        });
    });

    // Lorsque l'on change le champs pour choisir un controller
    $('#inputController').change(function(e){
        var bundleName = $('#inputBundle').val();
        var controllerName = $(this).val();
        $.get('<?= App::generateUrl("framework_admin_form_route_getActionList_route") ?>', {bundleName: bundleName, controllerName: controllerName}, function(donnees){
            $('#inputAction').html(donnees);
            $('#btn-add-action').show(); // Quand on selectionne un controller, on affiche le bouton pour ajouter une action
        });
    });


    // Lorsque l'on clique sur le bouton pour creer un bundle
    $('#btn-add-bundle').click(function (e) {
        e.preventDefault();

        let bundleName = prompt("Nom du bundle :");
        let bundleAuthor = prompt("Auteur du bundle :");
        let bundleUrl = prompt("Url du bundle :");

        $.post('<?= App::generateUrl("framework_admin_form_bundle_route") ?>', {'inputNom': bundleName, 'inputAuteur': bundleAuthor, 'inputLink': bundleUrl}, function (donnees) {
            loadBundles(); // On recharge la liste de tout les bundles
        });
    });

    // Lorsque l'on clique sur le bouton pour créer un controller
    $('#btn-add-controller').click(function (e) {
        e.preventDefault();

        let bundleName = $('#inputBundle').val();
        let controllerName = prompt("Nom du controller :");

        $.post('<?= App::generateUrl("framework_admin_form_controller_route") ?>', {'inputNom' : controllerName, 'inputBundle': bundleName}, function (donnees) {
            $('#inputBundle').trigger('change'); // On récupére la liste des controllers pour le bundle correspondant
        });
    });

    // Lorsque l'on clique sur le bouton pour créer une action
    $('#btn-add-action').click(function (e) {
        e.preventDefault();

        let bundleName = $('#inputBundle').val();
        let controllerName = $('#inputController').val();
        let actionName = prompt("Nom de l'action :");

        $.get('<?= App::generateUrl("framework_admin_form_action_route") ?>', {'inputBundle': bundleName, 'inputController' : controllerName, 'inputAction': actionName}, function (donnees) {
            $('#inputController').trigger('change'); // On récupére la liste des actions pour le bundle et le controller correspondant
        });
    });
</script>