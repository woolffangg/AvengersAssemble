<?php
require_once __DIR__ . '/../service/UserService.php';

class UserController {
    public function login() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = UserService::login($_POST['login'], $_POST['mdp']);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: index.php?action=salons');
                exit;
            } else {
                $error = 'Identifiants incorrects';
                require __DIR__ . '/../view/login.php';
            }
        } else {
            require __DIR__ . '/../view/login.php';
        }
    }
    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = $_POST['pseudo'] ?? '';
            $login = $_POST['login'] ?? '';
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['mdp'] ?? '';
            if ($pseudo && $login && $email && $mdp) {
                $result = UserService::register($pseudo, $login, $mdp, $email);
                if ($result === true) {
                    $success = 'Inscription réussie, vous pouvez vous connecter.';
                } else {
                    $error = $result;
                }
            } else {
                $error = 'Tous les champs sont obligatoires';
            }
            require __DIR__ . '/../view/register.php';
        } else {
            require __DIR__ . '/../view/register.php';
        }
    }
}
