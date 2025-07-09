<?php
require_once '../controller/UserController.php';
require_once '../controller/SalonController.php';

$action = $_GET['action'] ?? 'login';

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
