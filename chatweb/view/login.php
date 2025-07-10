<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container">
    <h2>Connexion</h2>
    <form method="post">
        <div class="form-group">
            <input type="text" name="login" placeholder="Login" required>
        </div>
        <div class="form-group">
            <input type="password" name="mdp" placeholder="Mot de passe" required>
        </div>
        <button type="submit">Se connecter</button>
        <?php if (!empty($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
    </form>
    <a href="index.php?action=register">Créer un compte</a>
</div>