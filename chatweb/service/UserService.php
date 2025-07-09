<?php
require_once __DIR__ . '/../model/UserRepository.php';

class UserService {
    public static function login($login, $mdp) {
        $user = UserRepository::findByLogin($login);
        if ($user && password_verify($mdp, $user['mdp'])) {
            return $user;
        }
        return null;
    }
    public static function register($pseudo, $login, $mdp, $email) {
        if (UserRepository::findByLogin($login)) {
            return 'Login déjà utilisé';
        }
        UserRepository::create($pseudo, $login, password_hash($mdp, PASSWORD_DEFAULT), $email);
        return true;
    }
}
