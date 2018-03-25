<?php
    use Core\Config\Config;
?>
<div>
    Bonjour <b><?= $nom ?></b> et bienvenue sur <?= Config::get('app:Email_SiteName') ?>
    <br>
    <br>Pour rappel vos identifiants de connexion sont :
    <br>
    <br>Votre adresse Email : <b> <?= $email ?></b>
    <br>Votre Mot de passe : <b> <?= $password ?></b>
    <br>
    <br>Cordialement,
    <br><?= Config::get('app:Email_Signature') ?>
</div>