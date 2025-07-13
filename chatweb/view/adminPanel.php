

<link rel="stylesheet" href="/AvengersAssemble/chatweb/public/css/admin.css">

<div class="container admin-container">
    <h2>Panneau d'administration des salons</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Propriétaire</th>
                <th>Topic</th>
                <th>Visibilité</th>
                <th>Privé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($salons as $salon): ?>
            <tr>
                <td><?= $salon['pkS'] ?></td>
                <td><?= htmlspecialchars($salon['nom']) ?></td>
                <td><?= htmlspecialchars($salon['proprio']) ?></td>
                <td><?= htmlspecialchars($salon['topic']) ?></td>
                <td><?= $salon['visibilite'] ? 'Oui' : 'Non' ?></td>
                <td><?= $salon['prive'] ? 'Oui' : 'Non' ?></td>
                <td>
                    <form method="post" action="index.php?action=deleteSalon" style="display:inline">
                        <input type="hidden" name="id" value="<?= $salon['pkS'] ?>">
                        <button type="submit" onclick="return confirm('Supprimer ce salon ?')">Supprimer</button>
                    </form>
                    <form method="post" action="index.php?action=joinSalon" style="display:inline">
                        <input type="hidden" name="id" value="<?= $salon['pkS'] ?>">
                        <button type="submit">Rejoindre</button>
                    </form>
                    <form method="post" action="index.php?action=editTopic" style="display:inline">
                        <input type="hidden" name="id" value="<?= $salon['pkS'] ?>">
                        <input type="text" name="topic" value="<?= htmlspecialchars($salon['topic']) ?>" size="10">
                        <button type="submit">Modifier topic</button>
                    </form>
                    <form method="post" action="index.php?action=changeOwner" style="display:inline">
                        <input type="hidden" name="id" value="<?= $salon['pkS'] ?>">
                        <select name="new_owner">
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['pkU'] ?>" <?= $user['pkU'] == $salon['fkU_proprio'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['pseudo']) ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                        <button type="submit">Changer proprio</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<a href="index.php" class="link-back">← Retour à l'accueil</a>
