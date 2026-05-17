<?php
/**
 * Page d'accueil du module
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

// Stats
$service = new PahekoProvisioningService($db);
$allInstances = $service->listInstances();
$totalInstances = count($allInstances);

$activeInstances = count(array_filter($allInstances, fn($i) => $i['status'] === 'active'));
$suspendedInstances = count(array_filter($allInstances, fn($i) => $i['status'] === 'suspended'));
$deletedInstances = count(array_filter($allInstances, fn($i) => $i['status'] === 'deleted'));

// Affichage
llxHeader('', $langs->trans('PahekoProvisioning'), '', '', '', '', '', '', '', 'mod-pahekoprovisioning');

print load_fiche_titre($langs->trans('PahekoProvisioning'), '', 'pahekoprovisioning@pahekoprovisioning');

// Stats
print '<div class="fiche center">';
print '<div class="fichehalfleft">';

print load_fiche_titre($langs->trans('Statistics'), '', '');

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Status').'</td>';
print '<td class="right">'.$langs->trans('Count').'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><span class="badge badge-success">'.$langs->trans('Active').'</span></td>';
print '<td class="right">'.$activeInstances.'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><span class="badge badge-warning">'.$langs->trans('Suspended').'</span></td>';
print '<td class="right">'.$suspendedInstances.'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><span class="badge badge-danger">'.$langs->trans('Deleted').'</span></td>';
print '<td class="right">'.$deletedInstances.'</td>';
print '</tr>';

print '<tr class="liste_total">';
print '<td><strong>'.$langs->trans('Total').'</strong></td>';
print '<td class="right"><strong>'.$totalInstances.'</strong></td>';
print '</tr>';

print '</table>';

print '</div>';
print '<div class="fichehalfright">';

print load_fiche_titre($langs->trans('QuickLinks'), '', '');

print '<div class="centpercent">';
print '<div class="tagtable centpercent borderborder">';

print '<div class="tagtr liste_titre">';
print '<div class="tagtd centpercent">'.$langs->trans('Action').'</div>';
print '</div>';

print '<div class="tagtr">';
print '<td class="tagtd centpercent">';
print '<a href="'.DOL_URL_ROOT.'/custom/pahekoprovisioning/instances/list.php">';
print img_picto('', 'folder').' '.$langs->trans('ViewInstances');
print '</a>';
print '</td>';
print '</div>';

print '<div class="tagtr">';
print '<td class="tagtd centpercent">';
print '<a href="'.DOL_URL_ROOT.'/custom/pahekoprovisioning/admin/setup.php">';
print img_picto('', 'config').' '.$langs->trans('Configuration');
print '</a>';
print '</td>';
print '</div>';

print '</div>';
print '</div>';

print '</div>';
print '</div>';

// Derniers logs
print '<br>';
print load_fiche_titre($langs->trans('RecentLogs'), '', '');

$sql = "SELECT l.rowid, l.event_type, l.message, l.created_at, i.instance_name, s.nom as soc_name";
$sql .= " FROM ".MAIN_DB_PREFIX."paheko_logs as l";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."paheko_instances as i ON l.fk_instance = i.rowid";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON i.fk_soc = s.rowid";
$sql .= " ORDER BY l.created_at DESC LIMIT 10";

$resql = $db->query($sql);
if ($resql) {
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans('Date').'</td>';
    print '<td>'.$langs->trans('Client').'</td>';
    print '<td>'.$langs->trans('Event').'</td>';
    print '<td>'.$langs->trans('Message').'</td>';
    print '</tr>';

    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.dol_print_date($db->jdate($obj->created_at), 'dayhour').'</td>';
        print '<td>'.dol_escape_htmltag($obj->soc_name).'</td>';
        print '<td><span class="badge badge-info">'.dol_escape_htmltag($obj->event_type).'</span></td>';
        print '<td>'.dol_escape_htmltag($obj->message).'</td>';
        print '</tr>';
    }

    print '</table>';
    $db->free($resql);
}

llxFooter();
$db->close();
