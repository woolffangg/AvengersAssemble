<?php require __DIR__ . '/partials/header.php'; ?>
<h2>Inscription</h2>
<form method="post">
    <input name="pseudo" placeholder="Pseudo" required>
    <input name="login" placeholder="Login" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="mdp" type="password" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
    <?php if (!empty($error)) echo '<div style="color:red">'.$error.'</div>'; ?>
    <?php if (!empty($success)) echo '<div style="color:green">'.$success.'</div>'; ?>
</form>
<a href="index.php?action=login">Déjà un compte ? Se connecter</a>
