<?php
// Contrôleur pour la gestion des salons

require_once __DIR__ . '/../model/Salon.php';
require_once __DIR__ . '/../model/Message.php';

class SalonController
{
    public function salons()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $salons = Salon::getAll();
        require __DIR__ . '/../view/salons.php';
    }
    public function chat()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $salon = Salon::getById($_GET['id'] ?? 1);
        $messages = Message::getBySalon($salon['pkS']);
        require __DIR__ . '/../view/chat.php';
    }
    public function sendMessage()
    {
        if (isset($_SESSION['user'], $_POST['message'], $_GET['id'])) {
            Message::add($_SESSION['user']['pkU'], $_GET['id'], $_POST['message']);
        }
        header('Location: index.php?action=chat&id=' . $_GET['id']);
        exit;
    }
    public function createSalon()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $topic = $_POST['topic'] ?? '';
            $prive = isset($_POST['prive']) ? 1 : 0;
            if ($nom) {
                $id = Salon::create($nom, $_SESSION['user']['pkU'], $topic, $prive);
                if ($id) {
                    $success = 'Salon créé !';
                    header('Location: index.php?action=salons');
                    exit;
                } else {
                    $error = 'Erreur lors de la création.';
                }
            } else {
                $error = 'Le nom est obligatoire';
            }
            require __DIR__ . '/../view/createSalon.php';
        } else {
            require __DIR__ . '/../view/createSalon.php';
        }
    }
}
