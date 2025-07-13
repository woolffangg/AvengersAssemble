<?php
// Classe Salon pour la gestion des salons
require_once __DIR__ . '/DB.php';

class Salon {
    // Ajoute un membre à un salon (invitation directe)
    public static function addMember($salonId, $userId) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT IGNORE INTO membre (fkU, fkS) VALUES (?, ?)');
        return $stmt->execute([$userId, $salonId]);
    }
    public static function getAll() {
        $db = DB::connect();
        return $db->query('SELECT * FROM Salon')->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all salons with owner pseudo
    public static function getAllWithOwner() {
        $db = DB::connect();
        $sql = 'SELECT s.*, u.pseudo AS proprio FROM Salon s LEFT JOIN Utilisateur u ON s.fkU_proprio = u.pkU';
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM Salon WHERE pkS = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM Salon WHERE pkS = ?');
        return $stmt->execute([$id]);
    }

    public static function updateTopic($id, $topic) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE Salon SET topic = ? WHERE pkS = ?');
        return $stmt->execute([$topic, $id]);
    }

    public static function changeOwner($id, $newOwner) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE Salon SET fkU_proprio = ? WHERE pkS = ?');
        return $stmt->execute([$newOwner, $id]);
    }
    public static function create($nom, $fkU_proprio, $topic = '', $prive = 0) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO Salon (nom, fkU_proprio, topic, prive) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$nom, $fkU_proprio, $topic, $prive])) {
            return $db->lastInsertId();
        }
        return false;
    }
}
