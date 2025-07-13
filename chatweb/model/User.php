<?php
require_once __DIR__ . '/DB.php';

class User
{
    public static function getAll() {
        $db = DB::connect();
        return $db->query('SELECT pkU, pseudo FROM Utilisateur')->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function findByLogin($login)
    {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM Utilisateur WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($pseudo, $login, $mdp, $email, $fkRole = 1)
    {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO Utilisateur (pseudo, login, mdp, email, fkRole) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$pseudo, $login, $mdp, $email, $fkRole]);
    }
}
