<form method="post" action="<?= App::generateUrl("framework_admin_form_controller_route") ?>">
    <!-- Modal -->
    <div class="modal fade" id="modalAddController" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Création d'un nouveau controller</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                            <div class="form-group">
                                <label for="inputNom">Nom du Controller</label>
                                <input class="form-control" id="inputNom" name="inputNom" placeholder="app, test, user ...">
                            </div>
                            <div class="form-group">
                                <label for="inputBundle">Nom du bundle</label>
                                <select name="inputBundle" class="form-control" id="inputBundle" >
                                    <option value="" selected>Choisir...</option>
                                    <?php foreach ($bundles as $bundle) : ?>
                                        <option value="<?= $bundle ?>"><?= $bundle ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                    <br>
                    <p style="margin-bottom: 0;">Le controller créer sera accesible sous l'identifiant suivant : <b id="nomBundle"></b>/<b id="nomController"></b></p>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $("#inputNom").keyup(function(e){
        $('#nomController').html( $(this).val() + "Controller" );
    });

    $("#inputBundle").change(function(e){
        $('#nomBundle').html( $(this).val());
    });
</script>