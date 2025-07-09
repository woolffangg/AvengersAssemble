<?php
// Classe Salon pour la gestion des salons
require_once __DIR__ . '/DB.php';

class Salon {
    public static function getAll() {
        $db = DB::connect();
        return $db->query('SELECT * FROM Salon')->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM Salon WHERE pkS = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
