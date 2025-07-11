<?php
// Classe Message pour la gestion des messages
require_once __DIR__ . '/DB.php';

class Message {
    public static function getBySalon($fkS) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT m.*, u.pseudo FROM Message m JOIN Utilisateur u ON m.fkU = u.pkU WHERE fkS = ? ORDER BY timestamp ASC');
        $stmt->execute([$fkS]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function add($fkU, $fkS, $message) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO Message (fkU, fkS, message) VALUES (?, ?, ?)');
        return $stmt->execute([$fkU, $fkS, $message]);
    }
    public static function getLastMessages($salonId) {
        $pdo = DB::connect();

        $sql = "SELECT m.pkMsg, u.pseudo, m.message, m.timestamp
                FROM Message m
                JOIN Utilisateur u ON u.pkU = m.fkU
                WHERE m.fkS = :salonId
                ORDER BY m.pkMsg ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['salonId' => $salonId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    
}
