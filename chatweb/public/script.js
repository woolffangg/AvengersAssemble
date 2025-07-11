// public/script.js

// Récupération de l’ID du salon
const salonInput = document.getElementById('salon-id');
const salonId    = salonInput ? salonInput.value : 1;
let lastId       = 0;

// Initialisation de l’EventSource
const evtSource = new EventSource(`public/sse.php?salon=${salonId}&lastId=${lastId}`);

evtSource.onmessage = function(event) {
  // On reçoit un seul message JSON
  const msg = JSON.parse(event.data);

  // Mise à jour du lastId pour la prochaine requête
  lastId = parseInt(event.lastEventId, 10);

  appendMessage(msg);
};

evtSource.onerror = function(err) {
  console.error('SSE error', err);
};  

// Fonction d’affichage d’un message dans le DOM
function appendMessage({ pseudo, message, timestamp }) {
  const container = document.getElementById('chat-box');
  const div       = document.createElement('div');
  div.classList.add('message');
  div.innerHTML = `
    <b>${pseudo}:</b>
    <span class="msg-text">${message}</span>
    <i class="msg-time">(${timestamp})</i>
  `;
  container.appendChild(div);
  div.scrollIntoView({ behavior: 'smooth' });
}
