
   
<?php
// Contrôleur pour la gestion des salons

require_once __DIR__ . '/../model/Salon.php';
require_once __DIR__ . '/../model/Message.php';
require_once __DIR__ . '/../service/SalonService.php';

class SalonController
{

    // Permet au proprio d'exclure un membre de son salon
    public function kickMember() {
        if (!isset($_SESSION['user'], $_POST['salon_id'], $_POST['user_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        $salon = Salon::getById($_POST['salon_id']);
        if ($salon && $salon['fkU_proprio'] == $_SESSION['user']['pkU']) {
            SalonService::removeMember($_POST['salon_id'], $_POST['user_id']);
        }
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }

    public function toggleVisibility() {
        if (!isset($_SESSION['user'], $_POST['salon_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        $salon = Salon::getById($_POST['salon_id']);
        if ($salon && $salon['fkU_proprio'] == $_SESSION['user']['pkU']) {
            $newVisibilite = $salon['visibilite'] ? 0 : 1;
            $db = DB::connect();
            $stmt = $db->prepare('UPDATE Salon SET visibilite = ? WHERE pkS = ?');
            $stmt->execute([$newVisibilite, $salon['pkS']]);
        }
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }
    // Permet à un utilisateur de quitter un salon privé
    public function quitSalon() {
        if (!isset($_SESSION['user'], $_POST['salon_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        $userId = $_SESSION['user']['pkU'];
        $salonId = $_POST['salon_id'];
        SalonService::removeMember($salonId, $userId);
        header('Location: index.php?action=salons');
        exit;
    }

    // Inviter un utilisateur dans un salon privé (proprio seulement)
    public function inviteMember() {
        if (!isset($_SESSION['user'], $_POST['salon_id'], $_POST['user_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        $salon = Salon::getById($_POST['salon_id']);
        if ($salon && $salon['fkU_proprio'] == $_SESSION['user']['pkU'] && $salon['prive']) {
            SalonService::addMember($_POST['salon_id'], $_POST['user_id']);
        }
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }
    // Admin panel for salons
    public function adminPanel() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        $salons = Salon::getAllWithOwner();
        require_once __DIR__ . '/../model/User.php';
        $users = User::getAll();
        require __DIR__ . '/../view/adminPanel.php';
    }

    // Delete a salon
    public function deleteSalon() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        if (isset($_POST['id'])) {
            Salon::delete($_POST['id']);
        }
        header('Location: index.php?action=adminPanel');
        exit;
    }

    // Join a salon as admin
    public function joinSalon() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        if (isset($_POST['id'])) {
            // Add admin as member if not already
            $db = DB::connect();
            $stmt = $db->prepare('INSERT IGNORE INTO membre (fkU, fkS) VALUES (?, ?)');
            $stmt->execute([$_SESSION['user']['pkU'], $_POST['id']]);
        }
        header('Location: index.php?action=chat&id=' . $_POST['id']);
        exit;
    }

    // Edit topic of a salon
    public function editTopic() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        if (isset($_POST['id'], $_POST['topic'])) {
            Salon::updateTopic($_POST['id'], $_POST['topic']);
        }
        header('Location: index.php?action=adminPanel');
        exit;
    }

    // Change owner of a salon
    public function changeOwner() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        if (isset($_POST['id'], $_POST['new_owner'])) {
            Salon::changeOwner($_POST['id'], $_POST['new_owner']);
        }
        header('Location: index.php?action=adminPanel');
        exit;
    }
    public function salons()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $userId = $_SESSION['user']['pkU'];
        $db = DB::connect();
        $sql = "SELECT s.* FROM Salon s
                LEFT JOIN membre m ON m.fkS = s.pkS AND m.fkU = ?
                WHERE s.prive = 0 OR m.fkU IS NOT NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $salons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../view/salons.php';
    }
    public function chat()
    {
        $start = microtime(true);
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $salon = Salon::getById($_GET['id'] ?? 1);
        $messages = Message::getBySalon($salon['pkS']);
        require __DIR__ . '/../view/chat.php';
        $end = microtime(true);
        error_log('chat.php load time: ' . round(($end - $start) * 1000) . ' ms');
    }
    public function sendMessage()
    {
        if (isset($_SESSION['user'], $_POST['message'], $_GET['id'])) {
            $userId = $_SESSION['user']['pkU'];
            $salonId = $_GET['id'];
            // Vérifie que l'utilisateur est membre du salon
            $db = DB::connect();
            $stmt = $db->prepare('SELECT 1 FROM membre WHERE fkU = ? AND fkS = ?');
            $stmt->execute([$userId, $salonId]);
            if ($stmt->fetch()) {
                Message::add($userId, $salonId, $_POST['message']);
                header('Location: index.php?action=chat&id=' . $salonId);
                exit;
            } else {
                // Redirige vers la liste des salons si non membre
                header('Location: index.php?action=salons');
                exit;
            }
        }
        header('Location: index.php?action=chat&id=' . ($_GET['id'] ?? 1));
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
                    // Ajoute le proprio comme membre
                    SalonService::addMember($id, $_SESSION['user']['pkU']);
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
