<?php
/**
 * Trigger Dolibarr pour Paheko Provisioning
 * 
 * @package   Dolibarr\Modules\PahekoProvisioning
 * @copyright 2026
 * @license   GPL v3
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
require_once __DIR__.'/../class/pahekoprovisioning.class.php';

/**
 * Classe InterfacePahekoProvisioning
 */
class InterfacePahekoProvisioning extends DolibarrTriggers
{
    /**
     * @var string Nom technique
     */
    public $name = 'PahekoProvisioning';

    /**
     * @var string Description
     */
    public $description = 'Provisioning automatique des instances Paheko';

    /**
     * @var string Version
     */
    public $version = '1.0.0';

    /**
     * @var int Priorité
     */
    public $priority = 50;

    /**
     * @var array Famille de triggers
     */
    public $family = 'pahekoprovisioning';

    /**
     * @var string Chemin du module
     */
    public $picto = 'pahekoprovisioning@pahekoprovisioning';

    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vérifie si un tiers a un contrat actif
     * 
     * @param int $socid ID du tiers
     * @return bool
     */
    private function hasActiveContract($socid)
    {
        global $db;
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."contrat";
        $sql .= " WHERE fk_soc = ".$socid;
        $sql .= " AND fk_statut = 2"; // 2 = Contrat actif
        
        $resql = $db->query($sql);
        if (!$resql) {
            return false;
        }
        
        $hasActive = ($db->num_rows($resql) > 0);
        $db->free($resql);
        
        return $hasActive;
    }

    /**
     * Fonction appelée après paiement facture
     * 
     * @param Facture $facture Facture Dolibarr
     * @param Translate $langs Langue
     * @param User $user Utilisateur
     * @param int $with_trigger 1=avec trigger
     * @return int 0=OK, -1=Erreur
     */
    public function runTrigger($action, $object, $user, $langs, $conf)
    {
        global $db;

        // Vérifier module activé
        if (empty($conf->pahekoprovisioning->enabled)) {
            return 0;
        }

        // Vérifier auto-provisioning activé
        if (empty($conf->global->PAHEKO_AUTO_PROVISIONING)) {
            return 0;
        }

        $service = new PahekoProvisioningService($db);

        switch ($action) {
            case 'CONTRACT_ACTIVATE':
                // Contrat activé -> créer instance
                if ($object instanceof Contrat) {
                    $socid = $object->fk_soc;
                    
                    // Vérifier si produit configuré correspond
                    $configuredProductRef = getDolGlobalString('PAHEKO_PRODUCT_REF');
                    if (!empty($configuredProductRef)) {
                        $hasPahekoProduct = false;
                        foreach ($object->lines as $line) {
                            if (!empty($line->fk_product)) {
                                $prod = new Product($db);
                                $prod->fetch($line->fk_product);
                                if ($prod->ref === $configuredProductRef) {
                                    $hasPahekoProduct = true;
                                    break;
                                }
                            }
                        }
                        if (!$hasPahekoProduct) {
                            dol_syslog('PahekoProvisioning::runTrigger Contrat sans produit Paheko, skip');
                            return 0;
                        }
                    }
                    
                    dol_syslog('PahekoProvisioning::runTrigger Contrat activé, socid='.$socid);
                    
                    $result = $service->createInstance($socid);
                    if ($result['success']) {
                        $this->setTMsg('Instance Paheko créée avec succès');
                    } else {
                        $this->setErrorMsg('Erreur création instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'CONTRACT_CLOSE':
                // Contrat résilié/terminé -> suspendre instance
                if ($object instanceof Contrat) {
                    $socid = $object->fk_soc;
                    dol_syslog('PahekoProvisioning::runTrigger Contrat résilié, socid='.$socid);
                    
                    $result = $service->suspendInstance($socid);
                    if (!$result['success']) {
                        $this->setErrorMsg('Erreur suspension instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'CONTRACT_DELETE':
                // Contrat supprimé -> supprimer instance
                if ($object instanceof Contrat) {
                    $socid = $object->fk_soc;
                    dol_syslog('PahekoProvisioning::runTrigger Contrat supprimé, socid='.$socid);
                    
                    $result = $service->deleteInstance($socid);
                    if (!$result['success']) {
                        $this->setErrorMsg('Erreur suppression instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'BILLING_INVOICE_PAID':
                // Facture payée -> vérifier si contrat actif avant de créer
                if ($object instanceof Facture) {
                    $socid = $object->fk_soc;
                    
                    // Vérifier s'il y a un contrat actif pour ce tiers
                    $hasActiveContract = $this->hasActiveContract($socid);
                    if (!$hasActiveContract) {
                        dol_syslog('PahekoProvisioning::runTrigger Pas de contrat actif, skip');
                        return 0;
                    }
                    
                    // Vérifier si produit configuré correspond
                    $configuredProductRef = getDolGlobalString('PAHEKO_PRODUCT_REF');
                    if (!empty($configuredProductRef)) {
                        $hasPahekoProduct = false;
                        foreach ($object->lines as $line) {
                            if ($line->ref === $configuredProductRef || $line->product_ref === $configuredProductRef) {
                                $hasPahekoProduct = true;
                                break;
                            }
                        }
                        if (!$hasPahekoProduct) {
                            dol_syslog('PahekoProvisioning::runTrigger Facture sans produit Paheko, skip');
                            return 0;
                        }
                    }
                    
                    dol_syslog('PahekoProvisioning::runTrigger Facture payée + contrat actif, socid='.$socid);
                    
                    $result = $service->createInstance($socid);
                    if ($result['success']) {
                        $this->setTMsg('Instance Paheko créée avec succès');
                    } else {
                        $this->setErrorMsg('Erreur création instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'BILLING_INVOICE_UNPAID':
                // Facture impayée -> suspendre instance
                if ($object instanceof Facture) {
                    $socid = $object->fk_soc;
                    dol_syslog('PahekoProvisioning::runTrigger Facture impayée, socid='.$socid);
                    
                    $result = $service->suspendInstance($socid);
                    if (!$result['success']) {
                        $this->setErrorMsg('Erreur suspension instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'THIRDPARTY_DELETE':
                // Suppression tiers -> supprimer instance
                if ($object instanceof Societe) {
                    $socid = $object->id;
                    dol_syslog('PahekoProvisioning::runTrigger Suppression tiers, socid='.$socid);
                    
                    $result = $service->deleteInstance($socid);
                    if (!$result['success']) {
                        $this->setErrorMsg('Erreur suppression instance: '.$result['message']);
                        return -1;
                    }
                }
                break;

            case 'SUBSCRIPTION_MODIFIED':
            case 'SUBSCRIPTION_DELETED':
                // Modification/suppression abonnement -> synchroniser
                if (method_exists($object, 'fk_soc')) {
                    $socid = $object->fk_soc;
                    dol_syslog('PahekoProvisioning::runTrigger Modification abonnement, socid='.$socid);
                    
                    $service->cronSyncInstances();
                }
                break;
        }

        return 0;
    }
}
