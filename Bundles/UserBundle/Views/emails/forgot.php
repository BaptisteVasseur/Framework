<?php
    use Core\Config\Config;
?>
<div>
    <b>Vous avez demandé à réinitialisé votre mot de passe</b>, voici votre nouveau code : <?= $generatedPassword ?>
    <br>
    <br>
    <u>N'oubliez pas de le changer si besoin ! </u>
    <br>
    <br>Cordialement,
    <br><?= Config::get('app:Email_Signature') ?>
</div>