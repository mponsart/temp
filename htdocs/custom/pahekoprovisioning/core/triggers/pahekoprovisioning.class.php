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
            case 'BILLING_INVOICE_PAID':
                // Facture payée -> créer instance
                if ($object instanceof Facture) {
                    $socid = $object->fk_soc;
                    
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
                    
                    dol_syslog('PahekoProvisioning::runTrigger Facture payée, socid='.$socid);
                    
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
