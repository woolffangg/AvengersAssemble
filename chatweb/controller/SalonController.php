
<?php
// controller/SalonController.php - Contrôleur pour la gestion des salons
require_once __DIR__ . '/../model/Salon.php';
require_once __DIR__ . '/../model/Message.php';
require_once __DIR__ . '/../service/SalonService.php';
require_once __DIR__ . '/../dao/SalonDAO.php';
require_once __DIR__ . '/../dao/MembreDAO.php';
require_once __DIR__ . '/../model/DB.php';

class SalonController
{
    /**
     * Permet au propriétaire d'exclure un membre de son salon
     */
    public function kickMember() {
        if (!isset($_SESSION['user'], $_POST['salon_id'], $_POST['user_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        $salonObj = Salon::getById($_POST['salon_id']);
        if ($salonObj && $salonObj->canUserManage($_SESSION['user']['pkU'])) {
            // Utiliser le service pour supprimer le membre
            SalonService::removeMember($_POST['salon_id'], $_POST['user_id']);
        }
        
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }

    /**
     * Basculer la visibilité d'un salon
     */
    public function toggleVisibility() {
        if (!isset($_SESSION['user'], $_POST['salon_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        $salonObj = Salon::getById($_POST['salon_id']);
        if ($salonObj && $salonObj->canUserManage($_SESSION['user']['pkU'])) {
            // Utiliser la méthode du modèle
            $salonObj->toggleVisibility();
        }
        
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }

    /**
     * Permet à un utilisateur de quitter un salon privé
     */
    public function quitSalon() {
        if (!isset($_SESSION['user'], $_POST['salon_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        $userId = $_SESSION['user']['pkU'];
        $salonId = $_POST['salon_id'];
        
        // Utiliser le service qui maintenant utilise les DAOs
        SalonService::removeMember($salonId, $userId);
        
        header('Location: index.php?action=salons');
        exit;
    }

    /**
     * Inviter un utilisateur dans un salon privé (propriétaire seulement)
     */
    public function inviteMember() {
        if (!isset($_SESSION['user'], $_POST['salon_id'], $_POST['user_id'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        $salonObj = Salon::getById($_POST['salon_id']);
        if ($salonObj && $salonObj->canUserManage($_SESSION['user']['pkU']) && $salonObj->getPrive()) {
            SalonService::addMember($_POST['salon_id'], $_POST['user_id']);
        }
        
        header('Location: index.php?action=chat&id=' . $_POST['salon_id']);
        exit;
    }

    /**
     * Panneau d'administration des salons
     */
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

    /**
     * Supprimer un salon (admin uniquement)
     */
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

    /**
     * Rejoindre un salon en tant qu'admin
     */
    public function joinSalon() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['fkRole'] != 2) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        if (isset($_POST['id'])) {
            // Ajouter l'admin comme membre s'il ne l'est pas déjà
            SalonService::addMember($_POST['id'], $_SESSION['user']['pkU']);
        }
        
        header('Location: index.php?action=chat&id=' . $_POST['id']);
        exit;
    }

    /**
     * Modifier le topic d'un salon
     */
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

    /**
     * Changer le propriétaire d'un salon
     */
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

    /**
     * Afficher la liste des salons
     */
    public function salons() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $userId = $_SESSION['user']['pkU'];
        $salons = SalonService::getSalonsForUser($userId);
        require __DIR__ . '/../view/salons.php';
    }

    /**
     * Afficher le chat d'un salon
     */
    public function chat() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $salonId = isset($_GET['id']) ? intval($_GET['id']) : 1;
        $salonObj = Salon::getById($salonId);
        
        if (!$salonObj || !$salonObj->canUserAccess($_SESSION['user']['pkU'])) {
            header('Location: index.php?action=salons');
            exit;
        }
        
        // Convertir l'objet Salon en tableau pour la compatibilité avec la vue
        $salon = $salonObj->toArray();
        $messages = Message::getBySalon($salonObj->getId());
        require_once __DIR__ . '/../model/User.php';
        $users = User::getAll();
        $membres = SalonService::getMembresSansProprio($salonObj->getId(), $salonObj->getProprietaireId());
        require __DIR__ . '/../view/chat.php';
    }

    /**
     * Envoyer un message
     */
    public function sendMessage() {
        if (isset($_SESSION['user'], $_POST['message'], $_GET['id'])) {
            $userId = $_SESSION['user']['pkU'];
            $salonId = $_GET['id'];
            
            // Vérifier que l'utilisateur peut poster dans ce salon
            if (Message::canUserPost($userId, $salonId)) {
                Message::add($userId, $salonId, $_POST['message']);
                header('Location: index.php?action=chat&id=' . $salonId);
                exit;
            } else {
                // Rediriger vers la liste des salons si non membre
                header('Location: index.php?action=salons');
                exit;
            }
        }
        
        header('Location: index.php?action=chat&id=' . ($_GET['id'] ?? 1));
        exit;
    }

    /**
     * Créer un nouveau salon
     */
    public function createSalon() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $topic = $_POST['topic'] ?? '';
            $prive = isset($_POST['prive']) ? 1 : 0;
            
            if ($nom) {
                $id = SalonService::createSalon($nom, $topic, $prive, $_SESSION['user']['pkU']);
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
