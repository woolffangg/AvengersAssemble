<?php
// controller/MessageController.php - Contrôleur pour la gestion des messages
require_once __DIR__ . '/../model/Message.php';
require_once __DIR__ . '/../dao/MessageDAO.php';
require_once __DIR__ . '/../dao/MembreDAO.php';
require_once __DIR__ . '/../model/DB.php';

class MessageController {

    /**
     * Stream des messages pour SSE
     */
    public function streamMessages() {
        // Les headers sont déjà envoyés par sse.php
        $salonId       = isset($_GET['salon'])  ? intval($_GET['salon'])  : 1;
        $lastMessageId = isset($_GET['lastId']) ? intval($_GET['lastId']) : 0;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['user']['pkU']) ? $_SESSION['user']['pkU'] : null;
        
        // Vérifie l'appartenance via le DAO
        $isMember = false;
        if ($userId) {
            $isMember = MembreDAO::isMember($userId, $salonId);
        }
        
        if (!$isMember) {
            // Expulse l'utilisateur du SSE
            echo "event: kick\ndata: {\"reason\":\"Vous avez été exclu de ce salon.\"}\n\n";
            @ob_flush();
            flush();
            return;
        }

        // Utilise le DAO pour récupérer les nouveaux messages
        $newMessages = MessageDAO::findNewMessages($salonId, $lastMessageId);
        error_log('[SSE PHP] lastMessageId reçu = ' . $lastMessageId);

        if (!empty($newMessages)) {
            $ids = array_map(fn($m) => $m['pkMsg'], $newMessages);
            error_log('[SSE PHP] IDs envoyés = ' . implode(',', $ids));
            foreach ($newMessages as $msg) {
                // On envoie toujours l'id du message dans l'event SSE
                echo "id: {$msg['pkMsg']}\n";
                echo 'data: ' . json_encode([
                    'pkMsg'     => $msg['pkMsg'],
                    'pseudo'    => $msg['pseudo'],
                    'message'   => $msg['message'],
                    'timestamp' => $msg['timestamp']
                ], JSON_UNESCAPED_UNICODE) . "\n\n";
            }
            $newMessages = null;
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

    /**
     * Récupère les messages d'un salon
     */
    public static function getMessages($salonId) {
        try {
            $messages = Message::getBySalon($salonId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des messages'
            ]);
        }
    }

    /**
     * Ajoute un nouveau message
     */
    public static function addMessage($userId, $salonId, $messageContent) {
        try {
            // Validation des paramètres
            if (empty($messageContent)) {
                return [
                    'success' => false,
                    'message' => 'Le message ne peut pas être vide'
                ];
            }

            // Vérifier que l'utilisateur peut poster dans ce salon
            if (!Message::canUserPost($userId, $salonId)) {
                return [
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à poster dans ce salon'
                ];
            }

            // Créer le message via le modèle
            $success = Message::add($userId, $salonId, $messageContent);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Message ajouté avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'ajout du message'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur interne du serveur'
            ];
        }
    }
}
