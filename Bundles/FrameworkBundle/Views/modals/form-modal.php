<form>
    <!-- Modal -->
    <div class="modal fade" id="modalAddFormStep1" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Création d'un nouveau formulaire</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputNom">Nom du fomulaire</label>
                            <input class="form-control" id="inputNom" placeholder="var1, var2, var3">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputBundle">Bundle</label>
                            <select name="" class="form-control" id="inputBundle">
                                <option selected>Choisir...</option>
                                <option value="">userBundle</option>
                                <option value="">frameworkBundle</option>
                                <option value="">testBundle</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputFile">Fichier</label>
                            <select name="" class="form-control" id="inputFile">
                                <option selected>Choisir...</option>
                                <option value="">loginForm</option>
                                <option value="">registerForm</option>
                                <option value="">profilForm</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputMethod">Methode</label>
                            <select name="" class="form-control" id="inputMethod">
                                <option selected>Choisir...</option>
                                <option value="">POST</option>
                                <option value="">GET</option>
                            </select>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="inputAction">Action (Url de traitement)</label>
                            <input class="form-control" id="inputAction" placeholder="http://">
                        </div>
                    </div>
                    <br>
                    <p>Pour récuperer le fomulaire, saisissez l'identifiant du formulaire suivant : <b>appBundle:loginForm:login</b></p>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddFormStep2">Suivant <i class="fa fa-arrow-alt-circle-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAddFormStep2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Création d'un nouveau formulaire</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="mt-0 mb-1">Champs du formulaire</h4>
                    <div class="card-body table-full-width table-responsive">
                        <table class="table table-hover">
                            <thead>

                            <tr>
                                <th>Nom</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Requis ?</th>
                                <th>Valeur par défaut</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th>
                                    <input type="text" class="form-control" placeholder="Nom du champs" name="inputNom">
                                </th>
                                <th>
                                    <input type="text" class="form-control" placeholder="Label du champs" name="inputLabel">
                                </th>
                                <th>
                                    <select name="" class="form-control" id="inputType">
                                        <option selected>Choisir un Type...</option>
                                        <option value="">text</option>
                                        <option value="">password</option>
                                        <option value="">email</option>
                                        <option value="">url</option>
                                        <option value="">phone</option>
                                        <option value="">date</option>
                                        <option value="">number</option>
                                        <option value="">range</option>
                                        <option value="">color</option>
                                        <option value="">textarea</option>
                                        <option value="">checkbox</option>
                                        <option value="">radio</option>
                                        <option value="">select</option>
                                        <option value="">YesNo</option>
                                        <option value="">submit</option>
                                        <option value="">cancel</option>
                                        <option value="">captcha</option>
                                        <option value="">file</option>
                                        <option value="">files</option>
                                        <option value="">autre</option>
                                    </select>
                                </th>
                                <th>
                                    <div class="checkbox" style="width: 65px;">
                                        <label style="width: 100px;">
                                            <input type="checkbox" value="" checked>
                                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                            Requis ?
                                        </label>
                                    </div>
                                </th>
                                <th>
                                    <input type="text" class="form-control" placeholder="Valeur par défaut" name="">
                                </th>
                                <th>
                                    <input type="number" class="form-control" placeholder="['nom' => 'valeur', 'nom' => 'valeur']" name="">
                                </th>
                            </tr>
                            </tbody>
                        </table>
                        <button class="btn btn-primary btn-xs">Ajouter une champ</button>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Annuler</button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddFormStep3">Suivant <i class="fa fa-arrow-alt-circle-right"></i></button>
                </div>
            </div>
        </div>
    </div>

</form>