<?php
/**
 * French language file for Paheko Provisioning
 * 
 * @package   Dolibarr\Modules\PahekoProvisioning
 * @copyright 2026
 * @license   GPL v3
 */

$langs = array(
    // Module
    'PahekoProvisioning' => 'Paheko Provisioning',
    'PahekoProvisioningDesc' => 'Provisioning automatique d\'instances Paheko',
    
    // Menu
    'Instances' => 'Instances',
    'Configuration' => 'Configuration',
    
    // Parameters
    'PAHEKO_INSTANCES_PATH' => 'Chemin des instances',
    'PAHEKO_INSTANCES_PATH_Desc' => 'Chemin absolu où créer les dossiers clients (ex: /home/user/paheko-clients)',
    'PAHEKO_AUTO_PROVISIONING' => 'Provisioning automatique',
    'PAHEKO_AUTO_PROVISIONING_Desc' => 'Activer la création automatique de dossiers lors du paiement des factures',
    
    // Actions
    'Save' => 'Enregistrer',
    'TestConfiguration' => 'Tester la configuration',
    'TestResults' => 'Résultats du test',
    'Suspend' => 'Suspendre',
    'Unsuspend' => 'Réactiver',
    'Delete' => 'Supprimer',
    
    // Status
    'Active' => 'Actif',
    'Suspended' => 'Suspendu',
    'Deleted' => 'Supprimé',
    'AllStatus' => 'Tous les statuts',
    
    // Instance
    'PahekoInstances' => 'Instances Paheko',
    'InstanceName' => 'Nom de l\'instance',
    'FolderPath' => 'Chemin du dossier',
    'CreatedAt' => 'Créé le',
    'NoInstance' => 'Aucune instance',
    
    // Confirmations
    'ConfirmSuspend' => 'Êtes-vous sûr de vouloir suspendre cette instance ?',
    'ConfirmUnsuspend' => 'Êtes-vous sûr de vouloir réactiver cette instance ?',
    'ConfirmDelete' => 'Êtes-vous sûr de vouloir supprimer cette instance ? Cette action est irréversible.',
    
    // Logs
    'SystemLogs' => 'Logs système',
    'RecentLogs' => 'Logs récents',
    'Event' => 'Événement',
    'Message' => 'Message',
    
    // Statistics
    'Statistics' => 'Statistiques',
    'Count' => 'Nombre',
    'Total' => 'Total',
    
    // Quick links
    'QuickLinks' => 'Liens rapides',
    'ViewInstances' => 'Voir les instances',
    
    // Events
    'CREATE' => 'Création',
    'SUSPEND' => 'Suspension',
    'UNSUSPEND' => 'Réactivation',
    'DELETE' => 'Suppression',
    
    // Errors
    'SetupSaved' => 'Configuration enregistrée',
    'Permissions insuffisantes' => 'Permissions insuffisantes',
    'Instance déjà existante pour ce tiers' => 'Instance déjà existante pour ce tiers',
    'Tiers non trouvé' => 'Tiers non trouvé',
    'Template non trouvé' => 'Template non trouvé',
    'Échec création dossier' => 'Échec de la création du dossier',
    'Échec copie template' => 'Échec de la copie du template',
    'Échec sauvegarde BDD' => 'Échec de la sauvegarde en base de données',
    'Instance créée avec succès' => 'Instance créée avec succès',
    'Instance suspendue' => 'Instance suspendue',
    'Instance réactivée' => 'Instance réactivée',
    'Instance supprimée' => 'Instance supprimée',
    'Instance déjà suspendue' => 'Instance déjà suspendue',
    'Instance non suspendue' => 'Instance non suspendue',
    'Instance non trouvée' => 'Instance non trouvée'
);
