<?php
/**
 * Classe PahekoProvisioningService
 * 
 * Gère le provisioning des instances Paheko
 * 
 * @package   Dolibarr\Modules\PahekoProvisioning
 * @copyright 2026
 * @license   GPL v3
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

class PahekoProvisioningService
{
    /**
     * @var DoliDB Database handler
     */
    private $db;

    /**
     * @var string Chemin du template
     */
    private $templatePath;

    /**
     * @var string Chemin des instances
     */
    private $instancesPath;

    /**
     * Constructeur
     * 
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->templatePath = getDolGlobalString('PAHEKO_TEMPLATE_PATH', '/home/user/paheko-template');
        $this->instancesPath = getDolGlobalString('PAHEKO_INSTANCES_PATH', '/home/user/paheko-clients');
    }

    /**
     * Crée une instance Paheko pour un tiers
     * 
     * @param int $socid ID du tiers Dolibarr
     * @return array ['success'=>bool, 'message'=>string, 'instance_id'=>int]
     */
    public function createInstance($socid)
    {
        global $conf, $langs;

        $result = array('success' => false, 'message' => '', 'instance_id' => 0);

        // Vérifier permissions
        if (!dolibarr_is_admin_or_has_right($socid, 'pahekoprovisioning', 'instances', 'write')) {
            $result['message'] = 'Permissions insuffisantes';
            return $result;
        }

        // Récupérer infos du tiers
        $soc = new Societe($this->db);
        $soc->fetch($socid);

        if (empty($soc->id)) {
            $result['message'] = 'Tiers non trouvé';
            return $result;
        }

        // Vérifier si instance existe déjà
        $existing = $this->getInstanceBySocId($socid);
        if ($existing) {
            $result['message'] = 'Instance déjà existante pour ce tiers';
            $result['instance_id'] = $existing['rowid'];
            return $result;
        }

        // Générer nom instance
        $instanceName = 'client_'.$socid.'_'.dol_print_date(dol_now(), '%Y%m%d%H%M%S');
        $folderPath = rtrim($this->instancesPath, '/').'/'.sanitize_file_name($instanceName);

        // Vérifier chemin template
        if (!is_dir($this->templatePath)) {
            $result['message'] = 'Template non trouvé: '.$this->templatePath;
            dol_syslog('PahekoProvisioning::createInstance Template non trouvé', LOG_ERR);
            return $result;
        }

        // Créer dossier instance
        if (!mkdir($folderPath, 0755, true)) {
            $result['message'] = 'Échec création dossier: '.$folderPath;
            dol_syslog('PahekoProvisioning::createInstance Échec mkdir', LOG_ERR);
            return $result;
        }

        // Copier template
        $copyResult = $this->copyTemplate($this->templatePath, $folderPath);
        if (!$copyResult) {
            $result['message'] = 'Échec copie template';
            return $result;
        }

        // Créer config.local.php
        $configContent = $this->generateConfigLocal($soc, $folderPath);
        $configPath = $folderPath.'/documents/conf/config.local.php';
        if (!dol_mkdir(dirname($configPath))) {
            dol_mkdir($folderPath.'/documents/conf');
        }
        file_put_contents($configPath, $configContent);

        // Sauvegarder en BDD
        $instanceId = $this->saveInstance($socid, $instanceName, $folderPath);
        if (!$instanceId) {
            $result['message'] = 'Échec sauvegarde BDD';
            return $result;
        }

        // Log
        $this->logEvent($instanceId, 'CREATE', 'Instance créée: '.$folderPath);

        $result['success'] = true;
        $result['message'] = 'Instance créée avec succès';
        $result['instance_id'] = $instanceId;

        dol_syslog('PahekoProvisioning::createInstance Instance créée ID='.$instanceId);

        return $result;
    }

    /**
     * Suspend une instance
     * 
     * @param int $socid ID du tiers Dolibarr
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function suspendInstance($socid)
    {
        global $conf, $langs;

        $result = array('success' => false, 'message' => '');

        // Vérifier permissions
        if (!dolibarr_is_admin_or_has_right($socid, 'pahekoprovisioning', 'instances', 'write')) {
            $result['message'] = 'Permissions insuffisantes';
            return $result;
        }

        // Récupérer instance
        $instance = $this->getInstanceBySocId($socid);
        if (!$instance) {
            $result['message'] = 'Instance non trouvée';
            return $result;
        }

        if ($instance['status'] === 'suspended') {
            $result['message'] = 'Instance déjà suspendue';
            $result['success'] = true;
            return $result;
        }

        // Créer fichier SUSPENDED
        $suspendedFile = rtrim($instance['folder_path'], '/').'/SUSPENDED';
        if (!file_put_contents($suspendedFile, 'Suspended at '.dol_print_date(dol_now(), '%Y-%m-%d %H:%M:%S'))) {
            $result['message'] = 'Échec création fichier SUSPENDED';
            return $result;
        }

        // Mettre à jour BDD
        $this->updateInstanceStatus($instance['rowid'], 'suspended');

        // Log
        $this->logEvent($instance['rowid'], 'SUSPEND', 'Instance suspendue');

        $result['success'] = true;
        $result['message'] = 'Instance suspendue';

        dol_syslog('PahekoProvisioning::suspendInstance Instance suspendue ID='.$instance['rowid']);

        return $result;
    }

    /**
     * Réactive une instance suspendue
     * 
     * @param int $socid ID du tiers Dolibarr
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function unsuspendInstance($socid)
    {
        global $conf, $langs;

        $result = array('success' => false, 'message' => '');

        // Vérifier permissions
        if (!dolibarr_is_admin_or_has_right($socid, 'pahekoprovisioning', 'instances', 'write')) {
            $result['message'] = 'Permissions insuffisantes';
            return $result;
        }

        // Récupérer instance
        $instance = $this->getInstanceBySocId($socid);
        if (!$instance) {
            $result['message'] = 'Instance non trouvée';
            return $result;
        }

        if ($instance['status'] !== 'suspended') {
            $result['message'] = 'Instance non suspendue';
            $result['success'] = true;
            return $result;
        }

        // Supprimer fichier SUSPENDED
        $suspendedFile = rtrim($instance['folder_path'], '/').'/SUSPENDED';
        if (file_exists($suspendedFile)) {
            unlink($suspendedFile);
        }

        // Mettre à jour BDD
        $this->updateInstanceStatus($instance['rowid'], 'active');

        // Log
        $this->logEvent($instance['rowid'], 'UNSUSPEND', 'Instance réactivée');

        $result['success'] = true;
        $result['message'] = 'Instance réactivée';

        dol_syslog('PahekoProvisioning::unsuspendInstance Instance réactivée ID='.$instance['rowid']);

        return $result;
    }

    /**
     * Supprime une instance
     * 
     * @param int $socid ID du tiers Dolibarr
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function deleteInstance($socid)
    {
        global $conf, $langs;

        $result = array('success' => false, 'message' => '');

        // Vérifier permissions
        if (!dolibarr_is_admin_or_has_right($socid, 'pahekoprovisioning', 'instances', 'delete')) {
            $result['message'] = 'Permissions insuffisantes';
            return $result;
        }

        // Récupérer instance
        $instance = $this->getInstanceBySocId($socid);
        if (!$instance) {
            $result['message'] = 'Instance non trouvée';
            return $result;
        }

        // Supprimer dossier
        $deleteResult = $this->deleteDirectory($instance['folder_path']);
        if (!$deleteResult) {
            $result['message'] = 'Échec suppression dossier';
            return $result;
        }

        // Mettre à jour BDD
        $this->updateInstanceStatus($instance['rowid'], 'deleted');

        // Log
        $this->logEvent($instance['rowid'], 'DELETE', 'Instance supprimée: '.$instance['folder_path']);

        $result['success'] = true;
        $result['message'] = 'Instance supprimée';

        dol_syslog('PahekoProvisioning::deleteInstance Instance supprimée ID='.$instance['rowid']);

        return $result;
    }

    /**
     * Synchronise les statuts des instances
     * 
     * @return array ['success'=>bool, 'processed'=>int, 'errors'=>int]
     */
    public function cronSyncInstances()
    {
        global $conf;

        $result = array('success' => true, 'processed' => 0, 'errors' => 0);

        // Récupérer toutes les instances actives
        $sql = "SELECT i.rowid, i.fk_soc, i.status, i.folder_path";
        $sql .= " FROM ".MAIN_DB_PREFIX."paheko_instances as i";
        $sql .= " WHERE i.status IN ('active', 'suspended')";
        $sql .= " AND i.deleted_at IS NULL";

        $resql = $this->db->query($sql);
        if (!$resql) {
            $result['success'] = false;
            return $result;
        }

        $num = $this->db->num_rows($resql);
        for ($i = 0; $i < $num; $i++) {
            $obj = $this->db->fetch_object($resql);

            // Vérifier statut factures du tiers
            $soc = new Societe($this->db);
            $soc->fetch($obj->fk_soc);

            $hasUnpaidInvoices = $this->hasUnpaidInvoices($obj->fk_soc);

            if ($hasUnpaidInvoices && $obj->status === 'active') {
                // Suspendre
                $this->suspendInstance($obj->fk_soc);
                $result['processed']++;
            } elseif (!$hasUnpaidInvoices && $obj->status === 'suspended') {
                // Réactiver
                $this->unsuspendInstance($obj->fk_soc);
                $result['processed']++;
            }
        }

        $this->db->free($resql);

        return $result;
    }

    /**
     * Copie récursive du template
     * 
     * @param string $src Source
     * @param string $dst Destination
     * @return bool
     */
    private function copyTemplate($src, $dst)
    {
        if (!is_dir($src)) {
            return false;
        }

        if (!dol_mkdir($dst)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $dstPath = str_replace($src, $dst, $file->getPathname());

            if ($file->isDir()) {
                dol_mkdir($dstPath);
            } else {
                copy($file->getPathname(), $dstPath);
            }
        }

        return true;
    }

    /**
     * Génère config.local.php
     * 
     * @param Societe $soc Tiers Dolibarr
     * @param string $folderPath Chemin instance
     * @return string Contenu config
     */
    private function generateConfigLocal($soc, $folderPath)
    {
        $domain = !empty($soc->url) ? $soc->url : 'client-'.$soc->id.'.local';
        
        $config = "<?php\n";
        $config .= "// Configuration générée automatiquement par Dolibarr\n";
        $config .= "// Tiers: ".$soc->name." (ID: ".$soc->id.")\n";
        $config .= "// Date: ".dol_print_date(dol_now(), '%Y-%m-%d %H:%M:%S')."\n\n";
        
        $config .= "\$dolibarr_main_url_root = 'https://".$domain."';\n";
        $config .= "\$dolibarr_main_document_root = '".$folderPath."';\n";
        $config .= "\$dolibarr_main_url_root_alt = '/custom';\n";
        $config .= "\$dolibarr_main_document_root_alt = '".$folderPath."/custom';\n";
        $config .= "\$dolibarr_main_data_root = '".$folderPath."/documents';\n";
        $config .= "\$dolibarr_main_db_host = 'localhost';\n";
        $config .= "\$dolibarr_main_db_user = 'paheko_".$soc->id."';\n";
        $config .= "\$dolibarr_main_db_pass = '".generateRandomPassword(16)."';\n";
        $config .= "\$dolibarr_main_db_name = 'paheko_".$soc->id."';\n";
        $config .= "\$dolibarr_main_db_prefix = 'llx_';\n";
        $config .= "\$dolibarr_main_db_type = 'mysqli';\n";
        $config .= "\$dolibarr_main_db_character_set = 'utf8mb4';\n";
        $config .= "\$dolibarr_main_db_collation = 'utf8mb4_unicode_ci';\n";
        $config .= "\$dolibarr_main_authentication = 'dolibarr';\n";
        $config .= "\$dolibarr_main_prod = 'on';\n";
        $config .= "\$dolibarr_main_force_https = 1;\n";
        $config .= "\$dolibarr_main_restrict_os_commands = 'mysqldump, mysql, pg_dump, pgrestore';\n";
        $config .= "\$dolibarr_nocsrfcheck = 0;\n";
        $config .= "\$dolibarr_main_instance_unique_id = 'paheko_".$soc->id."_".dol_now()."';\n";
        
        return $config;
    }

    /**
     * Sauvegarde instance en BDD
     * 
     * @param int $socid ID tiers
     * @param string $instanceName Nom instance
     * @param string $folderPath Chemin dossier
     * @return int|false ID instance ou false
     */
    private function saveInstance($socid, $instanceName, $folderPath)
    {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."paheko_instances (fk_soc, instance_name, folder_path, status, created_at)";
        $sql .= " VALUES (".$this->db->escape($socid).", ";
        $sql .= "        '".$this->db->escape($instanceName)."',";
        $sql .= "        '".$this->db->escape($folderPath)."',";
        $sql .= "        'active',";
        $sql .= "        NOW())";

        $resql = $this->db->query($sql);
        if (!$resql) {
            dol_syslog('PahekoProvisioning::saveInstance Erreur SQL: '.$this->db->lasterror(), LOG_ERR);
            return false;
        }

        return $this->db->last_insert_id(MAIN_DB_PREFIX.'paheko_instances');
    }

    /**
     * Met à jour statut instance
     * 
     * @param int $instanceId ID instance
     * @param string $status Nouveau statut
     * @return bool
     */
    private function updateInstanceStatus($instanceId, $status)
    {
        $sql = "UPDATE ".MAIN_DB_PREFIX."paheko_instances SET status = '".$this->db->escape($status)."'";
        
        if ($status === 'suspended') {
            $sql .= ", suspended_at = NOW()";
        } elseif ($status === 'active') {
            $sql .= ", suspended_at = NULL";
        } elseif ($status === 'deleted') {
            $sql .= ", deleted_at = NOW()";
        }

        $sql .= " WHERE rowid = ".$instanceId;

        return $this->db->query($sql);
    }

    /**
     * Récupère instance par ID tiers
     * 
     * @param int $socid ID tiers
     * @return array|null
     */
    private function getInstanceBySocId($socid)
    {
        $sql = "SELECT rowid, fk_soc, instance_name, folder_path, domain, status, created_at, suspended_at, deleted_at";
        $sql .= " FROM ".MAIN_DB_PREFIX."paheko_instances";
        $sql .= " WHERE fk_soc = ".$socid;
        $sql .= " ORDER BY rowid DESC LIMIT 1";

        $resql = $this->db->query($sql);
        if (!$resql) {
            return null;
        }

        $obj = $this->db->fetch_object($resql);
        $this->db->free($resql);

        return $obj ? (array) $obj : null;
    }

    /**
     * Vérifie si tiers a factures impayées
     * 
     * @param int $socid ID tiers
     * @return bool
     */
    private function hasUnpaidInvoices($socid)
    {
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."facture";
        $sql .= " WHERE fk_soc = ".$socid;
        $sql .= " AND fk_statut = 1"; // Statut: 1 = Non payée
        $sql .= " AND date_lim_reglement < NOW()";

        $resql = $this->db->query($sql);
        if (!$resql) {
            return false;
        }

        $hasUnpaid = ($this->db->num_rows($resql) > 0);
        $this->db->free($resql);

        return $hasUnpaid;
    }

    /**
     * Supprime dossier récursivement
     * 
     * @param string $dir Chemin dossier
     * @return bool
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        return rmdir($dir);
    }

    /**
     * Log un événement
     * 
     * @param int $instanceId ID instance
     * @param string $type Type événement
     * @param string $message Message
     * @return bool
     */
    public function logEvent($instanceId, $type, $message)
    {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."paheko_logs (fk_instance, event_type, message, created_at)";
        $sql .= " VALUES (".$instanceId.", ";
        $sql .= "        '".$this->db->escape($type)."',";
        $sql .= "        '".$this->db->escape($message)."',";
        $sql .= "        NOW())";

        return $this->db->query($sql);
    }

    /**
     * Récupère logs d'une instance
     * 
     * @param int $instanceId ID instance
     * @return array
     */
    public function getInstanceLogs($instanceId)
    {
        $logs = array();

        $sql = "SELECT rowid, fk_instance, event_type, message, created_at";
        $sql .= " FROM ".MAIN_DB_PREFIX."paheko_logs";
        $sql .= " WHERE fk_instance = ".$instanceId;
        $sql .= " ORDER BY created_at DESC";

        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $logs[] = (array) $obj;
            }
            $this->db->free($resql);
        }

        return $logs;
    }

    /**
     * Liste toutes les instances
     * 
     * @param array $filter Filtres optionnels
     * @return array
     */
    public function listInstances($filter = array())
    {
        $instances = array();

        $sql = "SELECT i.rowid, i.fk_soc, i.instance_name, i.folder_path, i.domain, i.status, i.created_at, i.suspended_at, i.deleted_at";
        $sql .= "       , s.nom as soc_name, s.client as soc_client";
        $sql .= " FROM ".MAIN_DB_PREFIX."paheko_instances as i";
        $sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON i.fk_soc = s.rowid";
        $sql .= " WHERE 1=1";

        if (!empty($filter['status'])) {
            $sql .= " AND i.status = '".$this->db->escape($filter['status'])."'";
        }

        if (!empty($filter['socid'])) {
            $sql .= " AND i.fk_soc = ".$filter['socid'];
        }

        $sql .= " ORDER BY i.created_at DESC";

        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $instances[] = (array) $obj;
            }
            $this->db->free($resql);
        }

        return $instances;
    }

    /**
     * Test de configuration
     * 
     * @return array ['success'=>bool, 'messages'=>array]
     */
    public function testConfiguration()
    {
        $result = array('success' => true, 'messages' => array());

        // Test template path
        if (!is_dir($this->templatePath)) {
            $result['success'] = false;
            $result['messages'][] = 'Template non trouvé: '.$this->templatePath;
        } else {
            $result['messages'][] = 'Template OK: '.$this->templatePath;
        }

        // Test instances path
        if (!is_writable($this->instancesPath)) {
            $result['success'] = false;
            $result['messages'][] = 'Instances path non accessible en écriture: '.$this->instancesPath;
        } else {
            $result['messages'][] = 'Instances path OK: '.$this->instancesPath;
        }

        // Test permissions
        if (!function_exists('mkdir')) {
            $result['success'] = false;
            $result['messages'][] = 'Fonction mkdir() non disponible';
        }

        if (!function_exists('copy')) {
            $result['success'] = false;
            $result['messages'][] = 'Fonction copy() non disponible';
        }

        return $result;
    }
}

/**
 * Génère mot de passe aléatoire
 * 
 * @param int $length Longueur
 * @return string
 */
function generateRandomPassword($length = 16)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }

    return $password;
}

/**
 * Sanitize nom fichier
 * 
 * @param string $name Nom à sanitizer
 * @return string
 */
function sanitize_file_name($name)
{
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
}
