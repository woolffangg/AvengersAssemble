<?php
require_once __DIR__ . '/DB.php';

class User {
    public static function getByLogin($login) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM Utilisateur WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($pseudo, $login, $mdp, $email, $fkRole = 1) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO Utilisateur (pseudo, login, mdp, email, fkRole) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$pseudo, $login, password_hash($mdp, PASSWORD_DEFAULT), $email, $fkRole]);
    }
}
