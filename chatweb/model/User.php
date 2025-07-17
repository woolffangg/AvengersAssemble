<?php
// model/User.php - Modèle métier pour les utilisateurs
require_once __DIR__ . '/../dao/UserDAO.php';

class User
{
    private $pkU;
    private $pseudo;
    private $login;
    private $email;
    private $fkRole;

    /**
     * Constructeur
     */
    public function __construct($pkU = null, $pseudo = null, $login = null, $email = null, $fkRole = 1) {
        $this->pkU = $pkU;
        $this->pseudo = $pseudo;
        $this->login = $login;
        $this->email = $email;
        $this->fkRole = $fkRole;
    }

    // Getters
    public function getId() { return $this->pkU; }
    public function getPseudo() { return $this->pseudo; }
    public function getLogin() { return $this->login; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->fkRole; }

    // Setters
    public function setPseudo($pseudo) { $this->pseudo = $pseudo; }
    public function setLogin($login) { $this->login = $login; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($fkRole) { $this->fkRole = $fkRole; }

    /**
     * Vérifie si l'utilisateur est un administrateur
     * @return bool
     */
    public function isAdmin() {
        return $this->fkRole == 2;
    }

    /**
     * Vérifie si l'utilisateur peut modérer un salon
     * @param array $salon
     * @return bool
     */
    public function canModerateSalon($salon) {
        return $this->isAdmin() || $salon['fkU_proprio'] == $this->pkU;
    }

    /**
     * Sauvegarde l'utilisateur en base
     * @return bool
     */
    public function save() {
        if ($this->pkU) {
            return UserDAO::update($this->pkU, [
                'pseudo' => $this->pseudo,
                'login' => $this->login,
                'email' => $this->email,
                'fkRole' => $this->fkRole
            ]);
        } else {
            return UserDAO::create($this->pseudo, $this->login, '', $this->email, $this->fkRole);
        }
    }

    // Méthodes statiques (délégation vers DAO)
    public static function getAll() {
        return UserDAO::findAll();
    }

    public static function findByLogin($login) {
        return UserDAO::findByLogin($login);
    }

    public static function findById($id) {
        return UserDAO::findById($id);
    }

    public static function create($pseudo, $login, $mdp, $email, $fkRole = 1) {
        return UserDAO::create($pseudo, $login, $mdp, $email, $fkRole);
    }

    /**
     * Vérifie si un login existe déjà
     * @param string $login
     * @return bool
     */
    public static function loginExists($login) {
        return UserDAO::loginExists($login);
    }

    /**
     * Vérifie si un pseudo existe déjà
     * @param string $pseudo
     * @return bool
     */
    public static function pseudoExists($pseudo) {
        return UserDAO::pseudoExists($pseudo);
    }

    /**
     * Vérifie si un email existe déjà
     * @param string $email
     * @return bool
     */
    public static function emailExists($email) {
        return UserDAO::emailExists($email);
    }

    /**
     * Authentifie un utilisateur avec son login et mot de passe
     * @param string $login
     * @param string $mdp
     * @return User|null
     */
    public static function authenticate($login, $mdp) {
        $userData = UserDAO::authenticate($login, $mdp);
        return $userData ? self::fromArray($userData) : null;
    }

    /**
     * Convertit l'objet en tableau pour compatibilité avec les vues
     * @return array
     */
    public function toArray() {
        return [
            'pkU' => $this->pkU,
            'pseudo' => $this->pseudo,
            'login' => $this->login,
            'email' => $this->email,
            'fkRole' => $this->fkRole
        ];
    }

    /**
     * Crée une instance User à partir d'un tableau de données
     * @param array $data
     * @return User|null
     */
    public static function fromArray($data) {
        if (!$data) return null;
        return new User(
            $data['pkU'] ?? null,
            $data['pseudo'] ?? null,
            $data['login'] ?? null,
            $data['email'] ?? null,
            $data['fkRole'] ?? 1
        );
    }
}
