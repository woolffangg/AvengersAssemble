<?php require __DIR__ . '/partials/header.php'; ?>
<h2>Connexion</h2>
<form method="post">
    <input name="login" placeholder="Login" required>
    <input name="mdp" type="password" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
    <?php if (!empty($error)) echo '<div style="color:red">'.$error.'</div>'; ?>
</form>
<a href="index.php?action=register">Créer un compte</a>