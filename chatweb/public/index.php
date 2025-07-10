<?php
session_start();

require_once '../controller/UserController.php';
require_once '../controller/SalonController.php';

$action = $_GET['action'] ?? 'login';

// Routes that don't require login
$publicRoutes = ['login', 'register', 'logout'];

// Redirect if trying to access protected routes while not logged in
if (!in_array($action, $publicRoutes) && !isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}

// Redirect if trying to access login/register while already logged in
if (in_array($action, ['login', 'register']) && isset($_SESSION['user'])) {
    header('Location: index.php?action=salons');
    exit;
}

switch ($action) {
    case 'login':
        (new UserController())->login();
        break;
    case 'register':
        (new UserController())->register();
        break;
    case 'logout':
        (new UserController())->logout();
        break;
    case 'salons':
        (new SalonController())->salons();
        break;
    case 'chat':
        (new SalonController())->chat();
        break;
    case 'sendMessage':
        (new SalonController())->sendMessage();
        break;
    case 'createSalon':
        (new SalonController())->createSalon();
        break;
    default:
        echo '404';
}
