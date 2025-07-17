<?php
// dao/UserDAO.php - Data Access Object pour les utilisateurs
require_once __DIR__ . '/../model/DB.php';

class UserDAO
{
    /**
     * Récupère tous les utilisateurs
     * @return array
     */
    public static function findAll() {
        $db = DB::connect();
        return $db->query('SELECT pkU, pseudo FROM utilisateur')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un utilisateur par son login
     * @param string $login
     * @return array|false
     */
    public static function findByLogin($login) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM utilisateur WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un utilisateur par son ID
     * @param int $id
     * @return array|false
     */
    public static function findById($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM utilisateur WHERE pkU = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Authentifie un utilisateur avec login et mot de passe
     * @param string $login
     * @param string $mdp
     * @return array|false
     */
    public static function authenticate($login, $mdp) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM utilisateur WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe avec password_verify pour les hashes bcrypt
        if ($user && password_verify($mdp, $user['mdp'])) {
            return $user;
        }
        return false;
    }

    /**
     * Vérifie si un login existe déjà
     * @param string $login
     * @return bool
     */
    public static function loginExists($login) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un pseudo existe déjà
     * @param string $pseudo
     * @return bool
     */
    public static function pseudoExists($pseudo) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE pseudo = ?');
        $stmt->execute([$pseudo]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un email existe déjà
     * @param string $email
     * @return bool
     */
    public static function emailExists($email) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crée un nouvel utilisateur
     * @param string $pseudo
     * @param string $login
     * @param string $mdp
     * @param string $email
     * @param int $fkRole
     * @return bool
     */
    public static function create($pseudo, $login, $mdp, $email, $fkRole = 1) {
        $db = DB::connect();
        $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO utilisateur (pseudo, login, mdp, email, fkRole) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$pseudo, $login, $hashedPassword, $email, $fkRole]);
    }

    /**
     * Met à jour un utilisateur
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data) {
        $db = DB::connect();
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "$field = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $sql = 'UPDATE utilisateur SET ' . implode(', ', $fields) . ' WHERE pkU = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Supprime un utilisateur
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        // Supprimer d'abord tous les messages de l'utilisateur
        require_once __DIR__ . '/MessageDAO.php';
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM message WHERE fkU = ?');
        $stmt->execute([$id]);
        $stmt = $db->prepare('DELETE FROM utilisateur WHERE pkU = ?');
        return $stmt->execute([$id]);
    }
}
