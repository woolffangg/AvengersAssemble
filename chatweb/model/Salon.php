<?php
// model/Salon.php - Modèle métier pour les salons
require_once __DIR__ . '/../dao/SalonDAO.php';
require_once __DIR__ . '/../dao/MembreDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class Salon
{
    private $pkS;
    private $nom;
    private $topic;
    private $fkU_proprio;
    private $prive;
    private $visibilite;

    /**
     * Constructeur
     */
    public function __construct($pkS = null, $nom = null, $topic = '', $fkU_proprio = null, $prive = 0, $visibilite = 1) {
        $this->pkS = $pkS;
        $this->nom = $nom;
        $this->topic = $topic;
        $this->fkU_proprio = $fkU_proprio;
        $this->prive = $prive;
        $this->visibilite = $visibilite;
    }

    // Getters
    public function getId() { return $this->pkS; }
    public function getNom() { return $this->nom; }
    public function getTopic() { return $this->topic; }
    public function getProprietaireId() { return $this->fkU_proprio; }
    public function getPrive() { return $this->prive; }
    public function getVisibilite() { return $this->visibilite; }

    // Setters
    public function setNom($nom) { $this->nom = $nom; }
    public function setTopic($topic) { $this->topic = $topic; }
    public function setPrive($prive) { $this->prive = $prive; }
    public function setVisibilite($visibilite) { $this->visibilite = $visibilite; }

    /**
     * Vérifie si un utilisateur peut accéder à ce salon
     * @param int $userId
     * @return bool
     */
    public function canUserAccess($userId) {
        // Salon public : tout le monde peut y accéder
        if (!$this->prive) {
            return true;
        }
        
        // Salon privé : vérifier l'appartenance
        return MembreDAO::isMember($userId, $this->pkS);
    }

    /**
     * Vérifie si un utilisateur peut gérer ce salon (propriétaire ou admin)
     * @param int $userId
     * @return bool
     */
    public function canUserManage($userId) {
        // Le propriétaire peut toujours gérer
        if ($this->fkU_proprio == $userId) {
            return true;
        }

        // Vérifier si c'est un admin
        $user = UserDAO::findById($userId);
        return $user && $user['fkRole'] == 2;
    }

    /**
     * Ajoute un membre au salon
     * @param int $userId
     * @return bool
     */
    public function addMember($userId) {
        return MembreDAO::addMember($userId, $this->pkS);
    }

    /**
     * Supprime un membre du salon
     * @param int $userId
     * @return bool
     */
    public function removeMember($userId) {
        return MembreDAO::removeMember($userId, $this->pkS);
    }

    /**
     * Bascule la visibilité du salon
     * @return bool
     */
    public function toggleVisibility() {
        $newVisibilite = $this->visibilite ? 0 : 1;
        $success = SalonDAO::updateVisibility($this->pkS, $newVisibilite);
        if ($success) {
            $this->visibilite = $newVisibilite;
        }
        return $success;
    }

    /**
     * Récupère les membres du salon
     * @return array
     */
    public function getMembers() {
        return MembreDAO::findMembersBySalon($this->pkS);
    }

    /**
     * Sauvegarde le salon en base
     * @return bool
     */
    public function save() {
        if ($this->pkS) {
            $data = [
                'nom' => $this->nom,
                'topic' => $this->topic,
                'prive' => $this->prive,
                'visibilite' => $this->visibilite
            ];
            return SalonDAO::update($this->pkS, $data);
        } else {
            $id = SalonDAO::create($this->nom, $this->fkU_proprio, $this->topic, $this->prive);
            if ($id) {
                $this->pkS = $id;
                return true;
            }
            return false;
        }
    }

    /**
     * Convertit l'objet en tableau pour compatibilité avec les vues
     * @return array
     */
    public function toArray() {
        return [
            'pkS' => $this->pkS,
            'nom' => $this->nom,
            'topic' => $this->topic,
            'fkU_proprio' => $this->fkU_proprio,
            'prive' => $this->prive,
            'visibilite' => $this->visibilite
        ];
    }

    /**
     * Crée une instance Salon à partir d'un tableau de données
     * @param array $data
     * @return Salon|null
     */
    public static function fromArray($data) {
        if (!$data) return null;
        return new Salon(
            $data['pkS'] ?? null,
            $data['nom'] ?? null,
            $data['topic'] ?? null,
            $data['fkU_proprio'] ?? null,
            $data['prive'] ?? null,
            $data['visibilite'] ?? null
        );
    }

    // Méthodes statiques (délégation vers DAO)
    public static function getById($id) {
        $data = SalonDAO::findById($id);
        return $data ? self::fromArray($data) : null;
    }

    public static function getAll() {
        return SalonDAO::findAll();
    }

    public static function getAllWithOwner() {
        return SalonDAO::findAllWithOwner();
    }

    public static function create($nom, $proprietaireId, $topic = '', $prive = 0) {
        return SalonDAO::create($nom, $proprietaireId, $topic, $prive);
    }

    public static function delete($id) {
        return SalonDAO::delete($id);
    }

    public static function updateTopic($id, $topic) {
        return SalonDAO::updateTopic($id, $topic);
    }

    public static function changeOwner($id, $newOwnerId) {
        return SalonDAO::changeOwner($id, $newOwnerId);
    }
}
