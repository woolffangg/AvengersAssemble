// public/script.js

// Récupération de l’ID du salon
const salonInput = document.getElementById('salon-id');
const salonId    = salonInput ? salonInput.value : 1;
let lastId       = 0;


let evtSource = null;
function startSSE() {
  evtSource = new EventSource(`public/sse.php?salon=${salonId}&lastId=${lastId}`);

  evtSource.onmessage = function(event) {
    const msg = JSON.parse(event.data);
    lastId = parseInt(event.lastEventId, 10);
    appendMessage(msg);
  };

  evtSource.addEventListener('kick', function(event) {
    alert('Vous avez été exclu de ce salon.');
    evtSource.close();
    window.location.href = 'index.php?action=salons';
  });

  evtSource.onerror = function(err) {
    evtSource.close();
    setTimeout(startSSE, 200); // relance après 200ms
  };
}

startSSE();

window.addEventListener('beforeunload', function() {
  if (evtSource) evtSource.close();
});

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
