<?php require __DIR__ . '/partials/header.php'; ?>
<div class="container">

  <h2>Inscription</h2>
  <form method="post">
    <div class="form-group">
      <input type="text" name="pseudo" placeholder="Pseudo" required>
    </div>
    <div class="form-group">
      <input type="text" name="login" placeholder="Login" required>
    </div>
    <div class="form-group">
      <input type="text" name="email" type="email" placeholder="Email" required>
    </div>
    <div class="form-group">
      <input name="mdp" type="password" placeholder="Mot de passe" required>
    </div>
    <button class="button" type="submit">S'inscrire</button>
    <?php if (!empty($error)) echo '<div style="color:red">' . $error . '</div>'; ?>
    <?php if (!empty($success)) echo '<div style="color:green">' . $success . '</div>'; ?>
  </form>
  <a href="index.php?action=login">Déjà un compte ? Se connecter</a>
</div>