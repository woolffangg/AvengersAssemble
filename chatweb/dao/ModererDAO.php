<?php
// dao/ModererDAO.php - DAO pour la gestion des modérateurs de salon
require_once __DIR__ . '/../model/DB.php';

class ModererDAO {
    /**
     * Ajoute un modérateur à un salon
     */
    public static function addModerator($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT IGNORE INTO moderer (fkU, fkS) VALUES (?, ?)');
        return $stmt->execute([$userId, $salonId]);
    }

    /**
     * Retire un modérateur d'un salon
     */
    public static function removeModerator($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM moderer WHERE fkU = ? AND fkS = ?');
        return $stmt->execute([$userId, $salonId]);
    }

    /**
     * Vérifie si un utilisateur est modérateur d'un salon
     */
    public static function isModerator($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT 1 FROM moderer WHERE fkU = ? AND fkS = ?');
        $stmt->execute([$userId, $salonId]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * Liste les modérateurs d'un salon
     */
    public static function getModerators($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT fkU FROM moderer WHERE fkS = ?');
        $stmt->execute([$salonId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
