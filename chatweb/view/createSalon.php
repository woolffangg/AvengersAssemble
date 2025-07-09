<?php require __DIR__ . '/partials/header.php'; ?>
<h2>Créer un nouveau salon</h2>
<form method="post">
    <input name="nom" placeholder="Nom du salon" required>
    <input name="topic" placeholder="Topic (optionnel)">
    <label><input type="checkbox" name="prive" value="1"> Privé</label>
    <button type="submit">Créer</button>
    <?php if (!empty($error)) echo '<div style="color:red">'.$error.'</div>'; ?>
    <?php if (!empty($success)) echo '<div style="color:green">'.$success.'</div>'; ?>
</form>
<a href="index.php?action=salons">Retour aux salons</a>
