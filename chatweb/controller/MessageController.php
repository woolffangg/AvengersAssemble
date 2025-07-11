<?php
// controller/MessageController.php

require_once __DIR__ . '/../model/Message.php';

class MessageController {
    public function streamMessages() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        $salonId       = isset($_GET['salon'])  ? intval($_GET['salon'])  : 1;
        $lastMessageId = isset($_GET['lastId']) ? intval($_GET['lastId']) : 0;

        while (! connection_aborted()) {
            // Récupère tous les messages du salon après lastMessageId
            $messages = Message::getLastMessages($salonId);

            // Filtre les nouveaux messages
            $newMessages = array_filter(
                $messages,
                fn($msg) => $msg['pkMsg'] > $lastMessageId
            );

            if (! empty($newMessages)) {
                // Met à jour l’ID max
                $lastMessageId = max(array_column($newMessages, 'pkMsg'));

                // Envoi de chaque message en SSE
                foreach ($newMessages as $msg) {
                    echo "id: {$msg['pkMsg']}\n";
                    echo 'data: ' . json_encode([
                        'pseudo'    => $msg['pseudo'],
                        'message'   => $msg['message'],
                        'timestamp' => $msg['timestamp']
                    ], JSON_UNESCAPED_UNICODE) . "\n\n";
                }

                ob_flush();
                flush();
            }

            // Pause de 2 secondes
            sleep(2);
        }
    }
}
