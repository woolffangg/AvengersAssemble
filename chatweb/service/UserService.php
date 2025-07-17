<?php
// service/UserService.php - Service pour la gestion des utilisateurs
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class UserService {
    
    /**
     * Connexion utilisateur
     */
    public static function login($login, $mdp) {
        try {
            // Utiliser la nouvelle méthode authenticate qui gère le hachage
            $user = User::authenticate($login, $mdp);
            if ($user) {
                return $user->toArray();
            }
            return null;
        } catch (Exception $e) {
            error_log('Erreur UserService::login: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Inscription utilisateur
     */
    public static function register($pseudo, $login, $mdp, $email) {
        try {
            // Vérifier si le login existe déjà
            if (User::loginExists($login)) {
                return 'Login déjà utilisé';
            }

            // Vérifier si le pseudo existe déjà
            if (User::pseudoExists($pseudo)) {
                return 'Pseudo déjà utilisé';
            }

            // Vérifier si l'email existe déjà
            if (User::emailExists($email)) {
                return 'Email déjà utilisé';
            }

            // Créer l'utilisateur (le hachage du mot de passe est géré dans UserDAO::create)
            $success = User::create($pseudo, $login, $mdp, $email, 1);
            
            if ($success) {
                return true;
            } else {
                return 'Erreur lors de l\'inscription';
            }
        } catch (Exception $e) {
            error_log('Erreur UserService::register: ' . $e->getMessage());
            return 'Erreur interne du serveur';
        }
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public static function getUserById($id) {
        try {
            $userData = UserDAO::findById($id);
            if ($userData) {
                return User::fromArray($userData);
            }
            return null;
        } catch (Exception $e) {
            error_log('Erreur UserService::getUserById: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère tous les utilisateurs
     */
    public static function getAllUsers() {
        try {
            return UserDAO::findAll();
        } catch (Exception $e) {
            error_log('Erreur UserService::getAllUsers: ' . $e->getMessage());
            return [];
        }
    }
}