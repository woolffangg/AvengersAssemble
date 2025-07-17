<?php
// model/Message.php - Modèle métier pour les messages
require_once __DIR__ . '/../dao/MessageDAO.php';
require_once __DIR__ . '/../dao/MembreDAO.php';

class Message
{
    private $pkMsg;
    private $fkU;
    private $fkS;
    private $message;
    private $timestamp;

    /**
     * Constructeur
     */
    public function __construct($pkMsg = null, $fkU = null, $fkS = null, $message = null, $timestamp = null) {
        $this->pkMsg = $pkMsg;
        $this->fkU = $fkU;
        $this->fkS = $fkS;
        $this->message = $message;
        $this->timestamp = $timestamp;
    }

    // Getters
    public function getId() { return $this->pkMsg; }
    public function getUserId() { return $this->fkU; }
    public function getSalonId() { return $this->fkS; }
    public function getMessage() { return $this->message; }
    public function getTimestamp() { return $this->timestamp; }

    // Setters
    public function setMessage($message) { $this->message = $message; }

    /**
     * Vérifie si l'utilisateur peut poster dans le salon
     * @param int $userId
     * @param int $salonId
     * @return bool
     */
    public static function canUserPost($userId, $salonId) {
        return MembreDAO::isMember($userId, $salonId);
    }

    /**
     * Vérifie si l'utilisateur peut modifier ce message
     * @param int $userId
     * @return bool
     */
    public function canUserEdit($userId) {
        // Le propriétaire du message peut l'éditer
        if ($this->fkU == $userId) {
            return true;
        }

        // L'admin peut tout éditer
        $user = UserDAO::findById($userId);
        return $user && $user['fkRole'] == 2;
    }

    /**
     * Valide le contenu du message
     * @return bool
     */
    public function isValid() {
        return !empty(trim($this->message)) && strlen($this->message) <= 1000;
    }

    /**
     * Nettoie et sécurise le contenu du message
     * @return string
     */
    public function getSafeMessage() {
        return htmlspecialchars(trim($this->message), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sauvegarde le message en base
     * @return bool
     */
    public function save() {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->pkMsg) {
            return MessageDAO::update($this->pkMsg, $this->message);
        } else {
            return MessageDAO::create($this->fkU, $this->fkS, $this->message);
        }
    }

    // Méthodes statiques (délégation vers DAO)
    public static function getBySalon($fkS) {
        return MessageDAO::findBySalon($fkS);
    }

    public static function getLastMessages($salonId) {
        return MessageDAO::findLastMessages($salonId);
    }

    public static function getNewMessages($salonId, $lastId) {
        return MessageDAO::findNewMessages($salonId, $lastId);
    }

    public static function add($fkU, $fkS, $message) {
        // Vérifier que l'utilisateur peut poster
        if (!self::canUserPost($fkU, $fkS)) {
            return false;
        }

        $messageObj = new Message(null, $fkU, $fkS, $message);
        return $messageObj->save();
    }

    /**
     * Crée une instance Message à partir d'un tableau de données
     * @param array $data
     * @return Message|null
     */
    public static function fromArray($data) {
        if (!$data) return null;
        return new Message(
            $data['pkMsg'] ?? null,
            $data['fkU'] ?? null,
            $data['fkS'] ?? null,
            $data['message'] ?? null,
            $data['timestamp'] ?? null
        );
    }
}
