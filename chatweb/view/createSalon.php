<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container">
    <h2>Créer un nouveau salon</h2>
    <form method="post">
        <div class="form-group">
            <input type="text" name="nom" placeholder="Nom du salon" required>
        </div>
        <div class="form-group">
            <input type="text" name="topic" placeholder="Topic (optionnel)">
        </div>
        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="prive" value="1"> Privé
            </label>
        </div>
        <button class="button" type="submit">Créer</button>

        <?php if (!empty($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
        <?php if (!empty($success)) echo '<div class="success">' . htmlspecialchars($success) . '</div>'; ?>
    </form>

    <a href="index.php?action=salons" class="link-back">← Retour aux salons</a>
</div>