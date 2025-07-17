<?php
// dao/MessageDAO.php - Data Access Object pour les messages
require_once __DIR__ . '/../model/DB.php';

class MessageDAO
{
    /**
     * Récupère tous les messages d'un salon avec les informations utilisateur
     * @param int $fkS
     * @return array
     */
    public static function findBySalon($fkS) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT m.*, u.pseudo FROM message m JOIN utilisateur u ON m.fkU = u.pkU WHERE fkS = ? ORDER BY timestamp ASC');
        $stmt->execute([$fkS]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les derniers messages d'un salon (pour SSE)
     * @param int $salonId
     * @return array
     */
    public static function findLastMessages($salonId) {
        $db = DB::connect();
        $sql = "SELECT m.pkMsg, u.pseudo, m.message, m.timestamp
                FROM message m
                JOIN utilisateur u ON u.pkU = m.fkU
                WHERE m.fkS = :salonId
                ORDER BY m.pkMsg ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['salonId' => $salonId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les messages récents d'un salon après un ID donné
     * @param int $salonId
     * @param int $lastId
     * @return array
     */
    public static function findNewMessages($salonId, $lastId) {
        $db = DB::connect();
        $sql = "SELECT m.pkMsg, u.pseudo, m.message, m.timestamp
                FROM message m
                JOIN utilisateur u ON u.pkU = m.fkU
                WHERE m.fkS = ? AND m.pkMsg > ?
                ORDER BY m.pkMsg ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$salonId, $lastId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un message par son ID
     * @param int $id
     * @return array|false
     */
    public static function findById($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT m.*, u.pseudo FROM message m JOIN utilisateur u ON m.fkU = u.pkU WHERE m.pkMsg = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouveau message
     * @param int $fkU
     * @param int $fkS
     * @param string $message
     * @return bool
     */
    public static function create($fkU, $fkS, $message) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO message (fkU, fkS, message) VALUES (?, ?, ?)');
        return $stmt->execute([$fkU, $fkS, $message]);
    }

    /**
     * Met à jour un message
     * @param int $id
     * @param string $message
     * @return bool
     */
    public static function update($id, $message) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE Message SET message = ? WHERE pkMsg = ?');
        return $stmt->execute([$message, $id]);
    }

    /**
     * Supprime un message
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM Message WHERE pkMsg = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Supprime tous les messages d'un salon
     * @param int $salonId
     * @return bool
     */
    public static function deleteBySalon($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM Message WHERE fkS = ?');
        return $stmt->execute([$salonId]);
    }

    /**
     * Compte le nombre de messages dans un salon
     * @param int $salonId
     * @return int
     */
    public static function countBySalon($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM Message WHERE fkS = ?');
        $stmt->execute([$salonId]);
        return (int) $stmt->fetchColumn();
    }
}
