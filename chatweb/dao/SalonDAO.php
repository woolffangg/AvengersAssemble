<?php
// dao/SalonDAO.php - Data Access Object pour les salons
require_once __DIR__ . '/../model/DB.php';

class SalonDAO
{
    /**
     * Récupère tous les salons
     * @return array
     */
    public static function findAll() {
        $db = DB::connect();
        return $db->query('SELECT * FROM salon')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les salons avec le pseudo du propriétaire
     * @return array
     */
    public static function findAllWithOwner() {
        $db = DB::connect();
        $sql = 'SELECT s.*, u.pseudo AS proprio FROM salon s LEFT JOIN utilisateur u ON s.fkU_proprio = u.pkU';
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un salon par son ID
     * @param int $id
     * @return array|false
     */
    public static function findById($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT * FROM salon WHERE pkS = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un salon par son ID avec les informations du propriétaire
     * @param int $id
     * @return array|false
     */
    public static function findByIdWithOwner($id) {
        $db = DB::connect();
        $stmt = $db->prepare('SELECT s.*, u.pseudo AS proprio FROM salon s LEFT JOIN utilisateur u ON s.fkU_proprio = u.pkU WHERE s.pkS = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les salons accessibles à un utilisateur
     * @param int $userId
     * @return array
     */
    public static function findAccessibleByUser($userId) {
        $db = DB::connect();
        $sql = "SELECT s.* FROM salon s
                LEFT JOIN membre m ON m.fkS = s.pkS AND m.fkU = ?
                WHERE s.prive = 0 OR m.fkU IS NOT NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau salon
     * @param string $nom
     * @param int $fkU_proprio
     * @param string $topic
     * @param int $prive
     * @return int|false
     */
    public static function create($nom, $fkU_proprio, $topic = '', $prive = 0) {
        $db = DB::connect();
        $stmt = $db->prepare('INSERT INTO salon (nom, fkU_proprio, topic, prive) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$nom, $fkU_proprio, $topic, $prive])) {
            return $db->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour un salon
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
        
        $sql = 'UPDATE salon SET ' . implode(', ', $fields) . ' WHERE pkS = ?';
        $stmt = $db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Met à jour le topic d'un salon
     * @param int $id
     * @param string $topic
     * @return bool
     */
    public static function updateTopic($id, $topic) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE salon SET topic = ? WHERE pkS = ?');
        return $stmt->execute([$topic, $id]);
    }

    /**
     * Change le propriétaire d'un salon
     * @param int $id
     * @param int $newOwner
     * @return bool
     */
    public static function changeOwner($id, $newOwner) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE salon SET fkU_proprio = ? WHERE pkS = ?');
        return $stmt->execute([$newOwner, $id]);
    }

    /**
     * Met à jour la visibilité d'un salon
     * @param int $id
     * @param int $visibilite
     * @return bool
     */
    public static function updateVisibility($id, $visibilite) {
        $db = DB::connect();
        $stmt = $db->prepare('UPDATE salon SET visibilite = ? WHERE pkS = ?');
        return $stmt->execute([$visibilite, $id]);
    }

    /**
     * Supprime un salon
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        // Supprimer d'abord tous les messages du salon
        require_once __DIR__ . '/MessageDAO.php';
        MessageDAO::deleteBySalon($id);
        $db = DB::connect();
        $stmt = $db->prepare('DELETE FROM salon WHERE pkS = ?');
        return $stmt->execute([$id]);
    }
}
