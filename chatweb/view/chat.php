<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container chat-container">
    <h2>Salon : <?= htmlspecialchars($salon['nom']) ?></h2>

    <div class="messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <b><?= htmlspecialchars($msg['pseudo']) ?>:</b>
                <span class="msg-text"><?= htmlspecialchars($msg['message']) ?></span>
                <i class="msg-time">(<?= $msg['timestamp'] ?>)</i>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="index.php?action=sendMessage&id=<?= $salon['pkS'] ?>" class="chat-form">
        <input name="message" placeholder="Votre message..." required autocomplete="off" />
        <button class="button" type="submit">Envoyer</button>
    </form>

    <a href="index.php?action=salons" class="link-back">← Retour aux salons</a>
</div>