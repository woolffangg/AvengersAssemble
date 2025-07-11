<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container chat-container">
    <h2>Salon : <?= htmlspecialchars($salon['nom']) ?></h2>

    <!-- Stockage de l’ID du salon pour le JS -->
    <input type="hidden" id="salon-id" value="<?= htmlspecialchars($salon['pkS']) ?>">

    <!-- Conteneur des messages -->
    <div class="messages" id="chat-box">
  <?php foreach ($messages as $msg): ?>
    <div 
      class="message" 
      data-msg-id="<?= intval($msg['pkMsg']) ?>">
      <b><?= htmlspecialchars($msg['pseudo']) ?>:</b>
      <span class="msg-text"><?= htmlspecialchars($msg['message']) ?></span>
      <i class="msg-time">(<?= $msg['timestamp'] ?>)</i>
    </div>
  <?php endforeach; ?>
</div>


    <!-- Formulaire d’envoi -->
    <form method="post" action="index.php?action=sendMessage&id=<?= $salon['pkS'] ?>" class="chat-form">
        <input name="message" placeholder="Votre message..." required autocomplete="off" />
        <button class="button" type="submit">Envoyer</button>
    </form>

    <a href="index.php?action=salons" class="link-back">← Retour aux salons</a>
</div>

<!-- Inclusion du script SSE -->
<script src="public/script.js"></script>

<script>
// On attend que tout le DOM soit chargé
document.addEventListener('DOMContentLoaded', () => {
  // Récupère le dernier message dans #chat-box
  const lastElem = document.querySelector('#chat-box .message:last-child');
  const lastId   = lastElem
    ? parseInt(lastElem.dataset.msgId, 10)
    : 0;

  console.log('Initial lastId =', lastId);

  // Récupère l’ID du salon
  const salonId = document.getElementById('salon-id').value;
  console.log('Salon ID =', salonId);

  // Instancie l’EventSource avec les bons paramètres
  const evtSource = new EventSource(
    `public/sse.php?salon=${salonId}&lastId=${lastId}`
  );

  evtSource.onmessage = event => {
    const msg = JSON.parse(event.data);
    console.log('Nouveau message reçu', msg, '– event.lastEventId =', event.lastEventId);

    // Met à jour lastId pour les requêtes suivantes
    lastId = parseInt(event.lastEventId, 10);

    appendMessage(msg);
  };



  function appendMessage({ pseudo, message, timestamp }) {
    const container = document.getElementById('chat-box');
    const div = document.createElement('div');
    div.classList.add('message');
    // On ajoute l'attribut data-msg-id pour le futur calcul de lastId
    div.dataset.msgId = lastId;
    div.innerHTML = `
      <b>${pseudo}:</b>
      <span class="msg-text">${message}</span>
      <i class="msg-time">(${timestamp})</i>
    `;
    container.appendChild(div);
    div.scrollIntoView({ behavior: 'smooth' });
  }
});
</script>
