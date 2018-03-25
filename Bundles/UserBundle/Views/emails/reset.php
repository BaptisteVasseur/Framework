<?php
    use Core\Config\Config;
?>
<div>
    <b>Vous avez demandé à réinitialisé votre mot de passe</b>, voici un lien permettant de le réinitialiser :
    <br>
    <br><a href="<?= $lien ?>"><?= $lien ?></a>
    <br>
    <br>Cordialement,
    <br><?= Config::get('app:Email_Signature') ?>
</div>