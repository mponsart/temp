<?php
/**
 * Page de liste des instances
 * 
 * @package   Dolibarr\Modules\PahekoProvisioning
 * @copyright 2026
 * @license   GPL v3
 */

if (!defined('NOTOKENRENEWAL')) {
    define('NOTOKENRENEWAL', 1);
}
if (!defined('NOREQUIREMENU')) {
    define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIREHTML')) {
    define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
    define('NOREQUIREAJAX', 1);
}

require_once __DIR__.'/../../../master.inc.php';
require_once __DIR__.'/../class/pahekoprovisioning.class.php';

// Vérifier permissions
if (!$user->hasRight('pahekoprovisioning', 'instances', 'read')) {
    accessforbidden();
}

// Langues
$langs->loadLangs(array('pahekoprovisioning@pahekoprovisioning'));

// Actions
$action = GETPOST('action', 'aZ09');
$instanceId = GETPOST('id', 'int');
$socid = GETPOST('socid', 'int');

$service = new PahekoProvisioningService($db);
$error = 0;

// Traiter actions
if ($action === 'confirm_suspend' && $socid) {
    if ($user->hasRight('pahekoprovisioning', 'instances', 'write')) {
        $result = $service->suspendInstance($socid);
        if ($result['success']) {
            setEventMessages($result['message'], null, 'mesgs');
        } else {
            setEventMessages($result['message'], null, 'errors');
            $error++;
        }
    } else {
        setEventMessages('Permissions insuffisantes', null, 'errors');
        $error++;
    }
}

if ($action === 'confirm_unsuspend' && $socid) {
    if ($user->hasRight('pahekoprovisioning', 'instances', 'write')) {
        $result = $service->unsuspendInstance($socid);
        if ($result['success']) {
            setEventMessages($result['message'], null, 'mesgs');
        } else {
            setEventMessages($result['message'], null, 'errors');
            $error++;
        }
    } else {
        setEventMessages('Permissions insuffisantes', null, 'errors');
        $error++;
    }
}

if ($action === 'confirm_delete' && $socid) {
    if ($user->hasRight('pahekoprovisioning', 'instances', 'delete')) {
        $result = $service->deleteInstance($socid);
        if ($result['success']) {
            setEventMessages($result['message'], null, 'mesgs');
        } else {
            setEventMessages($result['message'], null, 'errors');
            $error++;
        }
    } else {
        setEventMessages('Permissions insuffisantes', null, 'errors');
        $error++;
    }
}

// Filtres
$filter = array();
if (GETPOST('filter_status', 'alpha')) {
    $filter['status'] = GETPOST('filter_status', 'alpha');
}

// Récupérer instances
$instances = $service->listInstances($filter);

// Affichage
llxHeader('', $langs->trans('PahekoInstances'), '', '', '', '', '', '', '', 'mod-pahekoprovisioning');

print load_fiche_titre($langs->trans('PahekoInstances'), '', 'pahekoprovisioning@pahekoprovisioning');

// Filtres
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" class="mb2">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<select name="filter_status" onchange="this.form.submit()">';
print '<option value="">'.$langs->trans('AllStatus').'</option>';
print '<option value="active"'.($filter['status'] === 'active' ? ' selected' : '').'>'.$langs->trans('Active').'</option>';
print '<option value="suspended"'.($filter['status'] === 'suspended' ? ' selected' : '').'>'.$langs->trans('Suspended').'</option>';
print '<option value="deleted"'.($filter['status'] === 'deleted' ? ' selected' : '').'>'.$langs->trans('Deleted').'</option>';
print '</select>';
print '</form>';

// Liste
print '<table class="liste centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Client').'</td>';
print '<td>'.$langs->trans('InstanceName').'</td>';
print '<td>'.$langs->trans('FolderPath').'</td>';
print '<td>'.$langs->trans('Status').'</td>';
print '<td>'.$langs->trans('CreatedAt').'</td>';
print '<td class="center">'.$langs->trans('Actions').'</td>';
print '</tr>';

if (empty($instances)) {
    print '<tr class="oddeven"><td colspan="6" class="center">'.$langs->trans('NoInstance').'</td></tr>';
} else {
    foreach ($instances as $instance) {
        $statusClass = '';
        $statusLabel = '';
        switch ($instance['status']) {
            case 'active':
                $statusClass = 'badge-success';
                $statusLabel = $langs->trans('Active');
                break;
            case 'suspended':
                $statusClass = 'badge-warning';
                $statusLabel = $langs->trans('Suspended');
                break;
            case 'deleted':
                $statusClass = 'badge-danger';
                $statusLabel = $langs->trans('Deleted');
                break;
        }

        print '<tr class="oddeven">';
        print '<td>';
        if ($instance['soc_name']) {
            print '<a href="'.DOL_URL_ROOT.'/societe/card.php?socid='.$instance['fk_soc'].'">';
            print dol_escape_htmltag($instance['soc_name']);
            print '</a>';
        } else {
            print 'ID: '.$instance['fk_soc'];
        }
        print '</td>';
        print '<td>'.dol_escape_htmltag($instance['instance_name']).'</td>';
        print '<td class="small">'.dol_escape_htmltag($instance['folder_path']).'</td>';
        print '<td><span class="badge '.$statusClass.'">'.$statusLabel.'</span></td>';
        print '<td>'.dol_print_date($db->jdate($instance['created_at']), 'day').'</td>';
        print '<td class="center">';
        
        if ($instance['status'] === 'active' || $instance['status'] === 'suspended') {
            // Bouton suspendre
            if ($instance['status'] === 'active' && $user->hasRight('pahekoprovisioning', 'instances', 'write')) {
                print '<a href="'.$_SERVER['PHP_SELF'].'?action=confirm_suspend&socid='.$instance['fk_soc'].'&token='.newToken().'"';
                print ' onclick="return confirm(\''.$langs->trans('ConfirmSuspend').'\')"';
                print '>'.$langs->trans('Suspend').'</a> &nbsp;';
            }
            
            // Bouton réactiver
            if ($instance['status'] === 'suspended' && $user->hasRight('pahekoprovisioning', 'instances', 'write')) {
                print '<a href="'.$_SERVER['PHP_SELF'].'?action=confirm_unsuspend&socid='.$instance['fk_soc'].'&token='.newToken().'">';
                print $langs->trans('Unsuspend');
                print '</a> &nbsp;';
            }
            
            // Bouton supprimer
            if ($user->hasRight('pahekoprovisioning', 'instances', 'delete')) {
                print '<a href="'.$_SERVER['PHP_SELF'].'?action=confirm_delete&socid='.$instance['fk_soc'].'&token='.newToken().'"';
                print ' onclick="return confirm(\''.$langs->trans('ConfirmDelete').'\')"';
                print ' class="error">'.$langs->trans('Delete').'</a>';
            }
        }
        
        print '</td>';
        print '</tr>';
    }
}

print '</table>';

llxFooter();
$db->close();
