<?php
/**
 * Descriptor du module Paheko Provisioning
 * 
 * @package   Dolibarr\Modules\PahekoProvisioning
 * @copyright 2026
 * @license   GPL v3
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Classe modPahekoProvisioning
 */
class modPahekoProvisioning extends DolibarrModules
{
    /**
     * Constructeur
     */
    public function __construct($db)
    {
        $this->db = $db;

        // Identifiant unique du module
        $this->numero = 999999;

        // Nom technique
        $this->name = preg_replace('/^mod/i', '', get_class($this));

        // Nom affiché
        $this->description = "Provisioning automatique d'instances Paheko";

        // Auteur
        $this->editor_name = 'Paheko Provisioning';
        $this->editor_url = 'https://paheko.fr';

        // Version du module
        $this->version = '1.0.0';

        // Clé de licence
        $this->module_license = 'GPLv3';

        // Chemin du module
        $this->dir = dirname(__DIR__).'/pahekoprovisioning/';
        $this->dir_output = 'pahekoprovisioning/';

        // Dépendances
        $this->hidden = false;
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();

        // Langue du module
        $this->langfiles = array("pahekoprovisioning@pahekoprovisioning");

        // Constantes du module
        $this->const = array(
            0 => array(
                'PAHEKO_TEMPLATE_PATH',
                'chaine',
                '/home/user/paheko-template',
                'Chemin du template Paheko',
                0,
                0
            ),
            1 => array(
                'PAHEKO_INSTANCES_PATH',
                'chaine',
                '/home/user/paheko-clients',
                'Chemin de stockage des instances',
                0,
                0
            ),
            2 => array(
                'PAHEKO_AUTO_PROVISIONING',
                'chaine',
                '1',
                'Activer le provisioning automatique',
                0,
                0
            )
        );

        // Tables du module
        $this->tables = array(
            'paheko_instances',
            'paheko_logs'
        );

        // Hooks
        $this->hooks = array(
            'thirdpartyCard',
            'invoiceCard',
            'subscriptionCard'
        );

        // Triggers
        $this->triggers = array(
            'BILLING',
            'THIRDPARTY',
            'SUBSCRIPTION'
        );

        // Droits par défaut
        $this->rights = array();
        $r = 0;

        $this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1);
        $this->rights[$r][1] = 'Voir les instances';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'instances';
        $this->rights[$r][5] = 'read';
        $r++;

        $this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1);
        $this->rights[$r][1] = 'Créer/Modifier instances';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'instances';
        $this->rights[$r][5] = 'write';
        $r++;

        $this->rights[$r][0] = $this->numero . sprintf('%02d', $r + 1);
        $this->rights[$r][1] = 'Supprimer instances';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'instances';
        $this->rights[$r][5] = 'delete';
        $r++;

        // Menus
        $this->menu = array();

        // Menu principal
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=home',
            'type' => 'top',
            'titre' => 'Paheko Provisioning',
            'mainmenu' => 'pahekoprovisioning',
            'leftmenu' => 'pahekoprovisioning',
            'url' => '/pahekoprovisioning/admin/index.php',
            'langs' => 'pahekoprovisioning@pahekoprovisioning',
            'position' => 100 + $r,
            'enabled' => '$conf->pahekoprovisioning->enabled',
            'perms' => '$user->hasRight("pahekoprovisioning", "instances", "read")',
            'target' => '',
            'user' => 2
        );
        $r++;

        // Sous-menu Instances
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=pahekoprovisioning',
            'type' => 'leftmenu',
            'titre' => 'Instances',
            'mainmenu' => 'pahekoprovisioning',
            'leftmenu' => 'pahekoprovisioning_instances',
            'url' => '/pahekoprovisioning/instances/list.php',
            'langs' => 'pahekoprovisioning@pahekoprovisioning',
            'position' => 100 + $r,
            'enabled' => '$conf->pahekoprovisioning->enabled',
            'perms' => '$user->hasRight("pahekoprovisioning", "instances", "read")',
            'target' => '',
            'user' => 2
        );
        $r++;

        // Sous-menu Configuration
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=pahekoprovisioning',
            'type' => 'leftmenu',
            'titre' => 'Configuration',
            'mainmenu' => 'pahekoprovisioning',
            'leftmenu' => 'pahekoprovisioning_setup',
            'url' => '/pahekoprovisioning/admin/setup.php',
            'langs' => 'pahekoprovisioning@pahekoprovisioning',
            'position' => 100 + $r,
            'enabled' => '$conf->pahekoprovisioning->enabled',
            'perms' => '$user->hasRight("pahekoprovisioning", "instances", "write")',
            'target' => '',
            'user' => 2
        );
        $r++;

        // Cron jobs
        $this->cronjobs = array(
            0 => array(
                'label' => 'Synchronisation instances Paheko',
                'jobtype' => 'method',
                'class' => '/pahekoprovisioning/class/pahekoprovisioning.class.php',
                'objectname' => 'PahekoProvisioningService',
                'method' => 'cronSyncInstances',
                'parameters' => '',
                'comment' => 'Vérifie la cohérence des instances',
                'frequency' => 1,
                'unitfrequency' => '3600',
                'status' => 1,
                'test' => '$conf->pahekoprovisioning->enabled'
            )
        );

        // Compatibility
        $this->module_parts = array(
            'triggers' => 1,
            'hooks' => 1,
            'css' => array(),
            'js' => array(),
            'tpl' => 0,
            'theme' => 0,
            'entities' => array()
        );
    }

    /**
     * Initialisation du module
     */
    public function init($options = '')
    {
        $r = null;

        // Création des tables
        $sql = array();

        return $this->_init($sql, $options);
    }

    /**
     * Désactivation du module
     */
    public function remove($options = '')
    {
        $r = null;

        // Suppression des tables
        $sql = array(
            "DELETE FROM ".MAIN_DB_PREFIX."paheko_instances",
            "DELETE FROM ".MAIN_DB_PREFIX."paheko_logs"
        );

        return $this->_remove($sql, $options);
    }
}
