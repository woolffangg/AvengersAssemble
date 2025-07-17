<?php
// dao/MembreDAO.php - Data Access Object pour les membres des salons
require_once __DIR__ . '/../model/DB.php';

class MembreDAO
{
    /**
     * Vérifie si un utilisateur est membre d'un salon
     * @param int $userId
     * @param int $salonId
     * @return bool
     */
    public static function isMember($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT 1 FROM membre WHERE fkU = ? AND fkS = ?');
        $stmt->execute([$userId, $salonId]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * Récupère tous les membres d'un salon avec leurs informations
     * @param int $salonId
     * @return array
     */
    public static function findMembersBySalon($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT u.pkU, u.pseudo FROM membre m JOIN utilisateur u ON u.pkU = m.fkU WHERE m.fkS = ?');
        $stmt->execute([$salonId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les membres d'un salon sans le propriétaire
     * @param int $salonId
     * @param int $proprietaireId
     * @return array
     */
    public static function findMembersExcludingOwner($salonId, $proprietaireId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT u.pkU, u.pseudo FROM membre m JOIN utilisateur u ON u.pkU = m.fkU WHERE m.fkS = ? AND u.pkU != ?');
        $stmt->execute([$salonId, $proprietaireId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Alias pour la méthode findMembersExcludingOwner (compatibilité)
     * @param int $salonId
     * @param int $proprietaireId
     * @return array
     */
    public static function findMembersExceptOwner($salonId, $proprietaireId) {
        return self::findMembersExcludingOwner($salonId, $proprietaireId);
    }

    /**
     * Récupère tous les salons d'un utilisateur
     * @param int $userId
     * @return array
     */
    public static function findSalonsByUser($userId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT s.* FROM membre m JOIN salon s ON s.pkS = m.fkS WHERE m.fkU = ?');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un membre à un salon
     * @param int $userId
     * @param int $salonId
     * @return bool
     */
    public static function addMember($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT IGNORE INTO membre (fkU, fkS) VALUES (?, ?)');
        return $stmt->execute([$userId, $salonId]);
    }

    /**
     * Supprime un membre d'un salon
     * @param int $userId
     * @param int $salonId
     * @return bool
     */
    public static function removeMember($userId, $salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM membre WHERE fkU = ? AND fkS = ?');
        return $stmt->execute([$userId, $salonId]);
    }

    /**
     * Supprime tous les membres d'un salon
     * @param int $salonId
     * @return bool
     */
    public static function removeAllMembers($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM membre WHERE fkS = ?');
        return $stmt->execute([$salonId]);
    }

    /**
     * Compte le nombre de membres dans un salon
     * @param int $salonId
     * @return int
     */
    public static function countMembersBySalon($salonId) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM membre WHERE fkS = ?');
        $stmt->execute([$salonId]);
        return (int) $stmt->fetchColumn();
    }
}
