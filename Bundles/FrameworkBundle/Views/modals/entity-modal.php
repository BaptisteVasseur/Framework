<form method="post" action="<?= App::generateUrl("framework_admin_form_entity_route") ?>">
    <!-- Modal -->
    <div class="modal fade" id="modalAddEntityStep1" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une entité</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label for="inputNom">Nom de l'entité</label>
                            <input class="form-control" id="inputNom" name="inputNom" placeholder="user, article, comments ..">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="inputBundle">Bundle</label>
                            <select name="inputBundle" class="form-control" id="inputBundle">
                                <option selected>Choisir...</option>
                                <?php foreach ($bundles as $bundle) : ?>
                                    <option value="<?= $bundle ?>"><?= $bundle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="genereateCrud" value="true">
                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                            Générer les formulaires et controllers CRUD
                        </label>
                    </div>
                    <br>
                    <p style="margin-bottom: 0;">
                        L'entitée qui va être créer portera le nom suivant : <b id="displayEntityName">(Veuillez saisir un nom d'entité)</b>.
                        <br>Elle sera accesible sous l'identifiant : <b id="displayEntityShortcutBundle">(Veuillez selectionner un bundle)</b><b>:</b><b id="displayEntityShortcutEntity">(Veuillez saisir un nom d'entité)</b>
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Fermer</button>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAddEntityStep2">Suivant <i class="fa fa-arrow-alt-circle-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddEntityStep2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une entité</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="mt-0 mb-1">Propriétés de l'entité</h4>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover">

                            <tbody id="propertiesTableBody">

                            <tr id="tr-example" style="display: none;">
                                <th>
                                    <input type="text" class="form-control" placeholder="Nom de la propriété" name="inputProperties[propertyName][]">
                                </th>
                                <th>
                                    <select name="inputProperties[propertyType][]" class="form-control selectTypeTrigger" id="inputBundle">
                                        <option selected>Choisir un Type...</option>
<!--                                        <option value="identifier">identifier</option>-->
                                        <option value="string">string</option>
                                        <option value="integer">integer</option>
                                        <option value="text">text</option>
                                        <option value="boolean">boolean</option>
                                        <option value="array">array</option>
                                        <option value="datetime">datetime</option>
                                        <option value="OneToOne">OneToOne</option>
                                        <option value="OneToMany">OneToMany</option>
                                        <option value="autre">autre</option>
                                    </select>
                                </th>
                                <th>
                                    <div class="checkbox">
                                        <label style="width: 70px;">
                                            <input type="hidden" name="inputProperties[propertyIsNullable][]" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
                                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                            Null ?
                                        </label>
                                    </div>
                                </th>
                                <th>
                                    <input type="text" class="form-control" placeholder="Valeur par défaut" name="inputProperties[propertyDefaultValue][]">
                                </th>
                                <th class="lengthTH" style="display: none;">
                                    <input type="number" class="form-control" placeholder="Longueur maximale (255max)" name="inputProperties[propertyLength][]">
                                </th>
                                <th class="relationTH" style="display: none;">
                                    <select name="inputProperties[bundleRelationTarget][]" class="form-control bundleInput" title="choose bundle">
                                        <option selected>Choisir un Bundle...</option>
                                        <?php foreach ($bundles as $bundle) : ?>
                                            <option value="<?= $bundle ?>"><?= $bundle ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="inputProperties[entityRelationTarget][]" class="form-control entitiesInput" title="choose entity">
                                        <option selected>Choisir une Entitée...</option>
                                    </select>
                                </th>
                            </tr>

                            </tbody>
                        </table>
                        <button class="btn btn-primary btn-xs btn-add-property">Ajouter une propriété</button>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Fermer</button>
                    <button class="btn btn-success btn-sm">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

</form>

<script>

    $(document).on('change', '.bundleInput' ,function(e){
        var bundleName = $(this).val();

        let element = $(this).parent().parent().find('.entitiesInput');
        console.log( element );

        $.get('<?= App::generateUrl("framework_admin_form_route_getEntityList_route") ?>', {bundleName: bundleName}, function(donnees){
            element.html(donnees);
        });
    });

    $(document).on('change', '.selectTypeTrigger', function(e){

        let parent = $(this).parent().parent();

        let lengthTH = parent.find('.lengthTH');
        let relationTH = parent.find('.relationTH');

        let val = $(this).val();

        if( val === 'OneToMany' ){
            lengthTH.hide();
            relationTH.show();

        }else if( val === 'OneToOne' ){
            lengthTH.hide();
            relationTH.show();

        }else if( val === 'string' ){
            lengthTH.show();
            relationTH.hide();
        }else{
            lengthTH.hide();
            relationTH.hide();
        }

    });

    $('.btn-add-property').click(function(e){
        e.preventDefault();
        $('#propertiesTableBody').append("<tr class='added'>" + $('#tr-example').html() + "</tr>");
    });

    $('#inputNom').keyup(function(e){
        $('#displayEntityName').html( $(this).val() + "Entity" );
        $('#displayEntityShortcutEntity').html( $(this).val() + "Entity" );
    });

    $('#inputBundle').change(function(e){
        $('#displayEntityShortcutBundle').html( $(this).val() );
    });

//    $('.displayEntityName')
//    $('.displayEntityShortcut')
</script>