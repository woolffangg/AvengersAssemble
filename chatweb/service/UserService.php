<?php
require_once __DIR__ . '/../model/User.php';

class UserService {
    public static function login($login, $mdp) {
        $user = User::findByLogin($login);
        if ($user && password_verify($mdp, $user['mdp'])) {
            return $user;
        }
        return null;
    }
    public static function register($pseudo, $login, $mdp, $email) {
        if (User::findByLogin($login)) {
            return 'Login déjà utilisé';
        }
        User::create($pseudo, $login, password_hash($mdp, PASSWORD_DEFAULT), $email);
        return true;
    }
}