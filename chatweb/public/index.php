<?php
session_start();

require_once '../controller/UserController.php';
require_once '../controller/SalonController.php';

$action = $_GET['action'] ?? 'login';

// Define routes that don't need the user to be logged in
$publicRoutes = ['login', 'register', 'logout'];

// Protect all other routes
if (!in_array($action, $publicRoutes) && !isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
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
