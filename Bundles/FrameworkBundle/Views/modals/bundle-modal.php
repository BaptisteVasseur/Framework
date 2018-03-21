<form method="post" action="<?= App::generateUrl("framework_admin_form_bundle_route") ?>">
    <!-- Modal -->
    <div class="modal fade" id="modalAddBundle" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Création d'un nouveau bundle</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputNom">Nom du bundle</label>
                            <input class="form-control" id="inputNom" name="inputNom" placeholder="app, test, user ...">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputAuteur">Auteur</label>
                            <input class="form-control" id="inputAuteur" name="inputAuteur" placeholder="John Doe, @NomAuteur">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputLink">Lien du site de l'auteur, lien page Github ...</label>
                        <input class="form-control" id="inputLink" name="inputLink" placeholder="http://github.com/">
                    </div>
                    <br>
                    <p style="margin-bottom: 0;">Le bundle créer portera le nom suivant : <b id="nomBundle">TestBundle</b></p>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button class="btn btn-primary btn-sm">Créer</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $("#inputNom").keyup(function(e){
        $('#nomBundle').html( $(this).val() + "Bundle" );
    });
</script>