<form action="<?= App::generateUrl("framework_admin_form_config_route") ?>" method="get">
    <!-- Modal -->
    <div class="modal fade" id="modalAddConfig" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout d'un paramétre de configuration</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputNom">Nom du paramétre</label>
                            <input class="form-control" id="inputNom" name="inputNom" placeholder="var1, var2, var3">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputValue">Valeur du paramétre</label>
                            <input class="form-control" id="inputValue" name="inputValue" placeholder="secretkey, 2k1llm483ds9">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputBundle">Fichier de configuration</label>
                        <select class="form-control" id="inputBundle" name="inputBundle">
                            <option selected>Choisir...</option>
                            <?php foreach ($bundles as $bundle) : ?>
                            <option value="<?= $bundle ?>"><?= $bundle ?>/config</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button class="btn btn-primary btn-sm">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
</form>