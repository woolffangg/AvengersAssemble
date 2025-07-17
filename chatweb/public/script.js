// public/script.js

// Récupération de l’ID du salon
const salonInput = document.getElementById('salon-id');
const salonId    = salonInput ? salonInput.value : 1;
let lastId       = 0;






let evtSource = null;
function startSSE() {
  setTimeout(() => {
    if (evtSource && evtSource.readyState === 1) {
      evtSource.close();
    }
  }, 2000);
  if (evtSource) {
    evtSource.close();
  }
  evtSource = new EventSource(`public/sse.php?salon=${salonId}&lastId=${lastId}`);
  let maxId = lastId;

  evtSource.onmessage = function(event) {
    const msg = JSON.parse(event.data);
    let id = null;
    if (event.lastEventId && !isNaN(event.lastEventId)) {
      id = parseInt(event.lastEventId, 10);
    } else if (msg.pkMsg) {
      id = parseInt(msg.pkMsg, 10);
    }
    if (id !== null && id > lastId) lastId = id;
    appendMessage(msg);
  };

  evtSource.addEventListener('kick', function(event) {
    alert('Vous avez été exclu de ce salon.');
    evtSource.close();
    window.location.href = 'index.php?action=salons';
  });

  evtSource.onerror = function(err) {
    evtSource.close();
    setTimeout(startSSE, 200);
  };

  evtSource.onopen = function() {};

  evtSource.onclose = function() {
    setTimeout(startSSE, 100);
  };

  // Suppression du setTimeout qui fermait la connexion SSE toutes les 2 secondes
}

startSSE();

window.addEventListener('beforeunload', function() {
  if (evtSource) evtSource.close();
});

// Fonction d’affichage d’un message dans le DOM
function appendMessage({ pkMsg, pseudo, message, timestamp }) {
  const container = document.getElementById('chat-box');
  if (pkMsg && document.getElementById('msg-' + pkMsg)) return; // déjà affiché
  const div = document.createElement('div');
  div.classList.add('message');
  if (pkMsg) div.id = 'msg-' + pkMsg;
  div.innerHTML = `
    <b>${pseudo}:</b>
    <span class="msg-text">${message}</span>
    <i class="msg-time">(${timestamp})</i>
  `;
  container.appendChild(div);
  div.scrollIntoView({ behavior: 'smooth' });
}
