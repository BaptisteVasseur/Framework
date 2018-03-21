<form method="post" action="">
    <div class="modal fade" id="modifyDatabaseConnectionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier les paramétres de connexion à la BDD</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputUsername">Nom d'utilisateur</label>
                            <input class="form-control" id="inputUsername" name="inputUsername" placeholder="Par défaut : root" data-default="root" value="<?= $dbUsername ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword">Mot de passe</label>
                            <input class="form-control" id="inputPassword" name="inputPassword" placeholder="Par défaut : vide" data-default="root" value="<?= $dbPassword ?>">
                        </div>
                        <div class="form-group col-md-8">
                            <label for="inputHost">URL de la BDD</label>
                            <input class="form-control" id="inputHost" name="inputHost" placeholder="Par défaut : localhost" data-default="localhost" value="<?= $dbHost ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputPort">Port utilisé</label>
                            <input class="form-control" id="inputPort" name="inputPort" placeholder="Par défaut : 3306" data-default="3306" value="<?= $dbPort ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="inputDBName">Nom de la base de données</label>
                            <?php $defaultDBName = "bdd_" . rand(100000000, 999999999); ?>
                            <input class="form-control" id="inputDBName" name="inputDBName" placeholder="Par défaut : <?= $defaultDBName ?>" data-default="<?= $defaultDBName ?>" value="<?= $dbDBname ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Fermer</button>
                    <button class="btn btn-info btn-sm" id="triggerConnect" style="margin-right: 10px;">Se connecter à la BDD <i class="fa fa-spinner fa-spin"></i></button>
                    <button class="btn btn-warning btn-sm" id="triggerCreate" style="margin-right: 10px; display: none;">Créer la BDD <i class="fa fa-plus-circle"></i></button>
                    <button class="btn btn-success btn-sm" id="triggerValidate" style="margin-right: 10px; display: none;">Enregister la configuration <i class="fa fa-check"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="updateSchemaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Liste des requetes SQL qui vont être executé pour mettre à jour la BDD</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Listes des requetes
            </div>
            <div class="modal-footer" style="justify-content: flex-end;">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="margin-right: 10px;">Fermer</button>
                <a href="<?= App::generateUrl('framework_admin_database_update_route') ?>" class="btn btn-warning btn-sm confirmRedirectionLinkSelector" data-confirmTitle="Voulez-vous vraiment mettre à jour la base de données ?" style="margin-right: 10px;">Mettre à jour la base de données <i class="fa fa-exclamation-triangle"></i></a>
            </div>
        </div>
    </div>
</div>

<script>

    let getDefaultIfEmpty = function($element){
        if($element.val() && $element.val().trim() !== ""){
            return $element.val();
        }else{
            return $element.attr('data-default');
        }
    };

    $(function(){

        $('#updateDatabaseTrigger').click(function(e){
           e.preventDefault();

           $.get("<?= App::generateUrl('framework_admin_database_getUpdateQueries_route') ?>", {}, function(donnees){
               console.log(donnees);
               $('#updateSchemaModal').modal('show');
               $('#updateSchemaModal').find('.modal-body').html(donnees);
            });
        });


        $('#triggerConnect').click(function(e){
            e.preventDefault();

            let inputUsername = getDefaultIfEmpty( $('#inputUsername') );
            let inputPassword = getDefaultIfEmpty( $('#inputPassword') );
            let inputHost = getDefaultIfEmpty( $('#inputHost') );
            let inputPort = getDefaultIfEmpty( $('#inputPort') );
            let inputDBName = getDefaultIfEmpty( $('#inputDBName') );

            let params = {
                inputUsername: inputUsername,
                inputPassword:inputPassword,
                inputHost:inputHost,
                inputPort:inputPort,
                inputDBName:inputDBName
            };

            $.get('<?= App::generateUrl('framework_admin_form_testConnection_database_route') ?>', params, function(donnees){
                if(donnees === 'success-with-database'){
                    $('#triggerConnect').hide();
                    $('#triggerValidate').show();
                    showNotif('#fullSuccessNotification');
                    //alert('Connexion au serveur SQL et à la base de données réussi.');

                }else if(donnees === 'success-without-database'){
                    $('#triggerConnect').hide();
                    $('#triggerCreate').show();
                    showNotif('#serveurConnexionSuccessNotification');
                    //alert('Connexion au serveur SQL réussi mais la base de données est manquante.');

                }else{
                    showNotif('#serveurConnexionFailedNotification');
                    //alert('Echec de la connexion au serveur SQL');
                    console.log(donnees);
                }
            })
        });

        $('#triggerCreate').click(function(e){
            e.preventDefault();

            let inputUsername = getDefaultIfEmpty( $('#inputUsername') );
            let inputPassword = getDefaultIfEmpty( $('#inputPassword') );
            let inputHost = getDefaultIfEmpty( $('#inputHost') );
            let inputPort = getDefaultIfEmpty( $('#inputPort') );
            let inputDBName = getDefaultIfEmpty( $('#inputDBName') );

            let params = {
                inputUsername: inputUsername,
                inputPassword:inputPassword,
                inputHost:inputHost,
                inputPort:inputPort,
                inputDBName:inputDBName
            };

            $.get('<?= App::generateUrl('framework_admin_form_create_database_route') ?>', params, function(donnees){
                if(donnees === 'created'){

                    showNotif('#databaseCreatedNotification');
                    //alert('La base de données a bien été créer');
                    $('#triggerCreate').hide();
                    $('#triggerValidate').show();
                }else{

                    showNotif('#databaseNotCreatedNotification');
                    //alert('La base de données n\'a pas pu être créer');
                    console.log(donnees);
                }
            })
        });

        $('#triggerValidate').click(function(e){
            e.preventDefault();

            let inputUsername = getDefaultIfEmpty( $('#inputUsername') );
            let inputPassword = getDefaultIfEmpty( $('#inputPassword') );
            let inputHost = getDefaultIfEmpty( $('#inputHost') );
            let inputPort = getDefaultIfEmpty( $('#inputPort') );
            let inputDBName = getDefaultIfEmpty( $('#inputDBName') );

            let params = {
                inputUsername: inputUsername,
                inputPassword:inputPassword,
                inputHost:inputHost,
                inputPort:inputPort,
                inputDBName:inputDBName
            };

            $.get('<?= App::generateUrl('framework_admin_form_saveConfig_database_route') ?>', params, function(donnees){
                if(donnees === 'saved'){

                    showNotif('#configSavedNotification');
                    //alert('Configuration sauvegardée !');
                    $('#triggerValidate').hide();
                    $('#triggerConnect').show();
                    $('#modifyDatabaseConnectionModal').modal('hide');
                }else{

                    showNotif('#configNotSavedNotification');
                    //alert('Erreur lors de l\'écriture de la configuration de la base de données dans le fichier Config/Database.xml');
                    console.log(donnees);
                }
            })
        });

        <?php if($forceModalDisplay) : ?>
            $('#modifyDatabaseConnectionModal').modal('show');
        <?php endif; ?>

    });

</script>

<div class="notifications">

    <div class="notify animated success" id="fullSuccessNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-check"></i>
        </div>
        <div class="info">
            <span>Connexion réussi !</span>
            <span>Connexion au serveur SQL et à la base de données réussi.</span>
        </div>
    </div>

    <div class="notify animated warning" id="serveurConnexionSuccessNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-info"></i>
        </div>
        <div class="info">
            <span>Connexion au serveur réussi !</span>
            <span>Connexion au serveur SQL réussi mais la base de données est manquante.</span>
        </div>
    </div>

    <div class="notify animated danger" id="serveurConnexionFailedNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-times"></i>
        </div>
        <div class="info">
            <span>Aie...</span>
            <span>La connexion au serveur SQL à échoué.</span>
        </div>
    </div>

    <div class="notify animated success" id="databaseCreatedNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-check"></i>
        </div>
        <div class="info">
            <span>Créer !</span>
            <span>La base de données a bien été créer.</span>
        </div>
    </div>

    <div class="notify animated danger" id="databaseNotCreatedNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-times"></i>
        </div>
        <div class="info">
            <span>Erreur !</span>
            <span>La base de données n'a pu être créer.</span>
        </div>
    </div>

    <div class="notify animated success" id="configSavedNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-check"></i>
        </div>
        <div class="info">
            <span>Configuration sauvegardée !</span>
            <span>Le site est mainteant liée à la base de donnée.</span>
        </div>
    </div>

    <div class="notify animated danger" id="configNotSavedNotification" style="display: none;">
        <div class="circle">
            <i class="fa fa-times"></i>
        </div>
        <div class="info">
            <span>Erreur !</span>
            <span>Erreur lors de l'écriture de la configuration de la base de données dans le fichier Config/Database.xml</span>
        </div>
    </div>

</div>

<script>

    let animationIn = 'slideInRight';
    let animationOut = 'slideOutRight';

    let showNotif = function(selecteur){
        let element = $(selecteur);
        element.removeClass(animationOut).addClass(animationIn).show();

        setTimeout( () => {
            element.removeClass(animationIn).addClass(animationOut);
            setTimeout( () => {
                element.hide();
            }, 1000 );
        }, 3000 );
    }

</script>