<?php
require_once __DIR__ . '/../model/Salon.php';
require_once __DIR__ . '/../model/DB.php';

class SalonService {
    public static function addMember($salonId, $userId) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT IGNORE INTO membre (fkU, fkS) VALUES (?, ?)');
        return $stmt->execute([$userId, $salonId]);
    }
    public static function removeMember($salonId, $userId) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM membre WHERE fkU = ? AND fkS = ?');
        return $stmt->execute([$userId, $salonId]);
    }
}
