<?php
// controller/MessageController.php

require_once __DIR__ . '/../model/Message.php';

class MessageController {
    public function streamMessages() {
        // Les headers sont déjà envoyés par sse.php
        $salonId       = isset($_GET['salon'])  ? intval($_GET['salon'])  : 1;
        $lastMessageId = isset($_GET['lastId']) ? intval($_GET['lastId']) : 0;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['user']['pkU']) ? $_SESSION['user']['pkU'] : null;
        // Vérifie l'appartenance une seule fois
        $isMember = false;
        if ($userId) {
            $pdo = DB::connect();
            $stmt = $pdo->prepare('SELECT 1 FROM membre WHERE fkU = ? AND fkS = ?');
            $stmt->execute([$userId, $salonId]);
            $isMember = $stmt->fetch() ? true : false;
        }
        if (!$isMember) {
            // Expulse l'utilisateur du SSE
            echo "event: kick\ndata: {\"reason\":\"Vous avez été exclu de ce salon.\"}\n\n";
            @ob_flush();
            flush();
            return;
        }

        $messages = Message::getLastMessages($salonId);
        $newMessages = array_filter(
            $messages,
            fn($msg) => $msg['pkMsg'] > $lastMessageId
        );

        if (!empty($newMessages)) {
            $lastMessageId = max(array_column($newMessages, 'pkMsg'));
            foreach ($newMessages as $msg) {
                echo "id: {$msg['pkMsg']}\n";
                echo 'data: ' . json_encode([
                    'pseudo'    => $msg['pseudo'],
                    'message'   => $msg['message'],
                    'timestamp' => $msg['timestamp']
                ], JSON_UNESCAPED_UNICODE) . "\n\n";
            }
            @ob_flush();
            flush();
            return;
        }

        // Si pas de nouveaux messages, attend 2 secondes puis ferme
        usleep(2000000); // 2s
        echo ": ping\n\n";
        @ob_flush();
        flush();
    }
}
