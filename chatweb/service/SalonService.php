<?php
// service/SalonService.php - Service pour la gestion des salons
require_once __DIR__ . '/../model/Salon.php';
require_once __DIR__ . '/../dao/SalonDAO.php';
require_once __DIR__ . '/../dao/MembreDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class SalonService {
    
    /**
     * Récupère les membres d'un salon hors propriétaire
     */
    public static function getMembresSansProprio($salonId, $proprioId) {
        try {
            return MembreDAO::findMembersExcludingOwner($salonId, $proprioId);
        } catch (Exception $e) {
            error_log('Erreur SalonService::getMembresSansProprio: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ajoute un membre à un salon
     */
    public static function addMember($salonId, $userId) {
        try {
            return MembreDAO::addMember($userId, $salonId);
        } catch (Exception $e) {
            error_log('Erreur SalonService::addMember: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un membre d'un salon
     */
    public static function removeMember($salonId, $userId) {
        try {
            return MembreDAO::removeMember($userId, $salonId);
        } catch (Exception $e) {
            error_log('Erreur SalonService::removeMember: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les salons accessibles à un utilisateur
     */
    public static function getSalonsForUser($userId) {
        try {
            return SalonDAO::findAccessibleByUser($userId);
        } catch (Exception $e) {
            error_log('Erreur SalonService::getSalonsForUser: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Crée un nouveau salon
     */
    public static function createSalon($nom, $description, $visibilite, $proprietaireId) {
        try {
            // visibilite=1 (public) => prive=1, visibilite=0 (privé) => prive=0
            $prive = ($visibilite == 1) ? 1 : 0;
            $salonId = SalonDAO::create($nom, $proprietaireId, $description, $prive);

            if ($salonId) {
                // Ajouter automatiquement le propriétaire comme membre
                MembreDAO::addMember($proprietaireId, $salonId);
                return $salonId;
            }

            return false;
        } catch (Exception $e) {
            error_log('Erreur SalonService::createSalon: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur peut accéder à un salon
     */
    public static function canUserAccess($userId, $salonId) {
        try {
            $salon = Salon::getById($salonId);
            return $salon ? $salon->canUserAccess($userId) : false;
        } catch (Exception $e) {
            error_log('Erreur SalonService::canUserAccess: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les détails d'un salon avec les informations du propriétaire
     */
    public static function getSalonWithOwner($salonId) {
        try {
            return SalonDAO::findByIdWithOwner($salonId);
        } catch (Exception $e) {
            error_log('Erreur SalonService::getSalonWithOwner: ' . $e->getMessage());
            return null;
        }
    }
}
