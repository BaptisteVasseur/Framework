<?php
    use Core\Config\Config;
?>
<div>
    Bonjour <b><?= $nom ?></b>
    <br>
    <br>Votre mot de passe vient d'être modifié (adresse ip : <b><?= $ip ?></b>).
    <br>
    <br>Votre nouveau mot de passe est <b> <?= $password ?></b>
    <br>
    <br><small>Si vous n'êtes pas à l'origine de ce changement, nous vous invitons à immédiatement changer votre mot de passe.</small>
    <br><small>N'hésitez pas à nous contacter en cas de problèmes.</small>
    <br>
    <br>Cordialement,
    <br><?= Config::get('app:Email_Signature') ?>
</div>