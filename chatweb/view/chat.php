<?php require __DIR__ . '/partials/header.php'; ?>
<!-- Page de chat -->
<h2>Salon : <?= htmlspecialchars($salon['nom']) ?></h2>
<div>
    <?php foreach ($messages as $msg): ?>
        <div><b><?= htmlspecialchars($msg['pseudo']) ?>:</b> <?= htmlspecialchars($msg['message']) ?> <i>(<?= $msg['timestamp'] ?>)</i></div>
    <?php endforeach; ?>
</div>
<form method="post" action="index.php?action=sendMessage&id=<?= $salon['pkS'] ?>">
    <input name="message" required>
    <button type="submit">Envoyer</button>
</form>
<a href="index.php?action=salons">Retour aux salons</a>
