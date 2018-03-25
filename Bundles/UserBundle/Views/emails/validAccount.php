<?php
    use Core\Config\Config;
?>
<div>
    Bonjour <b><?= $nom ?></b> et bienvenue sur <?= Config::get('app:Email_SiteName') ?>
    <br>
    <br>Vos devez confirmer votre compte grâce au lien suivant : <a href="<?= $lien ?>"><?= $lien ?></a>.
    <br>
    <br>Une fois votre compte confirmé, vous pourrez vous connecter grâce à vos identifiants de connexion :
    <br>
    <br>Votre adresse Email : <b> <?= $email ?></b>
    <br>Votre Mot de passe : <b> <?= $password ?></b>
    <br>
    <br>Cordialement,
    <br><?= Config::get('app:Email_Signature') ?>
</div>