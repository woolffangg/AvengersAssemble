<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container chat-container">
<?php if (isset($_SESSION['user']) && $salon['fkU_proprio'] == $_SESSION['user']['pkU']): ?>
    <?php
        require_once __DIR__ . '/../model/User.php';
        $users = User::getAll();
        // Récupérer les membres du salon (hors proprio)
        $db = DB::connect();
        $stmt = $db->prepare('SELECT u.pkU, u.pseudo FROM membre m JOIN Utilisateur u ON u.pkU = m.fkU WHERE m.fkS = ? AND u.pkU != ?');
        $stmt->execute([$salon['pkS'], $_SESSION['user']['pkU']]);
        $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <form method="post" action="index.php?action=inviteMember" style="margin-bottom:18px;display:flex;gap:10px;align-items:center;">
        <input type="hidden" name="salon_id" value="<?= $salon['pkS'] ?>">
        <label for="invite-user" style="font-weight:600;">Inviter un membre :</label>
        <select name="user_id" id="invite-user" style="padding:4px 10px;border-radius:6px;border:1.5px solid #1a7fd733;">
            <?php foreach ($users as $user): ?>
                <?php if ($user['pkU'] != $_SESSION['user']['pkU']): ?>
                    <option value="<?= $user['pkU'] ?>"><?= htmlspecialchars($user['pseudo']) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="background:#1a7fd7;color:#fff;border:none;border-radius:6px;padding:6px 16px;font-weight:700;">Inviter</button>
    </form>
    <!-- Liste déroulante pour exclure un membre -->
    <?php if (count($membres) > 0): ?>
    <form method="post" action="index.php?action=kickMember" style="margin-bottom:18px;display:flex;gap:10px;align-items:center;">
        <input type="hidden" name="salon_id" value="<?= $salon['pkS'] ?>">
        <label for="kick-user" style="font-weight:600;">Exclure un membre :</label>
        <select name="user_id" id="kick-user" style="padding:4px 10px;border-radius:6px;border:1.5px solid #e74c3c;">
            <?php foreach ($membres as $m): ?>
                <option value="<?= $m['pkU'] ?>"><?= htmlspecialchars($m['pseudo']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="background:#e74c3c;color:#fff;border:none;border-radius:6px;padding:6px 16px;font-weight:700;">Exclure</button>
    </form>
    <?php endif; ?>
    <!-- Bouton pour changer la visibilité -->
    <form method="post" action="index.php?action=toggleVisibility" style="margin-bottom:18px;display:inline-block;">
        <input type="hidden" name="salon_id" value="<?= $salon['pkS'] ?>">
        <button type="submit" style="background:#27ae60;color:#fff;border:none;border-radius:6px;padding:6px 16px;font-weight:700;">
            Rendre ce salon <?= $salon['visibilite'] ? 'privé' : 'public' ?>
        </button>
    </form>
<?php endif; ?>
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

<?php if (isset($_SESSION['user']) && $salon['prive'] && $salon['fkU_proprio'] != $_SESSION['user']['pkU']): ?>
    <form method="post" action="index.php?action=quitSalon" style="margin:18px 0 0 0;">
        <input type="hidden" name="salon_id" value="<?= $salon['pkS'] ?>">
        <button type="submit" style="background:#e74c3c;color:#fff;border:none;border-radius:6px;padding:7px 18px;font-weight:700;">Quitter ce salon privé</button>
    </form>
<?php endif; ?>
</div>


<!-- Inclusion du script SSE -->
<script src="public/script.js"></script>
<script>
// Ferme le SSE avant navigation ou soumission de formulaire
document.addEventListener('DOMContentLoaded', function() {
  if (typeof evtSource !== 'undefined') {
    // Sur tous les liens
    document.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e) {
        evtSource.close();
      });
    });
    // Sur tous les formulaires
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        evtSource.close();
      });
    });
  }
});
</script>

<script>
// On attend que tout le DOM soit chargé
document.addEventListener('DOMContentLoaded', () => {

  // Récupère le dernier message dans #chat-box
  const lastElem = document.querySelector('#chat-box .message:last-child');
  let lastId   = lastElem
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
