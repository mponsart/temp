<?php
/**
 * Page de configuration du module
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Vérifier permissions
if (!$user->hasRight('pahekoprovisioning', 'instances', 'write')) {
    accessforbidden();
}

// Langues
$langs->loadLangs(array('admin', 'pahekoprovisioning@pahekoprovisioning'));

// Action
$action = GETPOST('action', 'aZ09');

// Test configuration
$testResult = null;
if ($action === 'test') {
    require_once __DIR__.'/../class/pahekoprovisioning.class.php';
    $service = new PahekoProvisioningService($db);
    $testResult = $service->testConfiguration();
}

// Sauvegarde configuration
if ($action === 'update' && !empty($user->admin)) {
    $instancesPath = GETPOST('PAHEKO_INSTANCES_PATH', 'alpha');
    $autoProvisioning = GETPOST('PAHEKO_AUTO_PROVISIONING', 'int');
    $productRef = GETPOST('PAHEKO_PRODUCT_REF', 'alpha');

    dolibarr_set_const($db, 'PAHEKO_INSTANCES_PATH', $instancesPath, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PAHEKO_AUTO_PROVISIONING', $autoProvisioning, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PAHEKO_PRODUCT_REF', $productRef, 'chaine', 0, '', $conf->entity);

    setEventMessages($langs->trans('SetupSaved'), null, 'mesgs');
}

// Affichage
llxHeader('', $langs->trans('PahekoProvisioningSetup'), '', '', '', '', '', '', '', 'mod-pahekoprovisioning');

print load_fiche_titre($langs->trans('PahekoProvisioningSetup'), '', 'pahekoprovisioning@pahekoprovisioning');

print '<br>';

// Formulaire configuration
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Parameter').'</td>';
print '<td>'.$langs->trans('Value').'</td>';
print '<td>'.$langs->trans('Description').'</td>';
print '</tr>';

// Instances path
print '<tr class="oddeven">';
print '<td><label for="PAHEKO_INSTANCES_PATH">'.$langs->trans('PAHEKO_INSTANCES_PATH').'</label></td>';
print '<td><input type="text" name="PAHEKO_INSTANCES_PATH" id="PAHEKO_INSTANCES_PATH" value="'.getDolGlobalString('PAHEKO_INSTANCES_PATH').'" class="minwidth400"></td>';
print '<td>'.$langs->trans('PAHEKO_INSTANCES_PATH_Desc').'</td>';
print '</tr>';

// Product ref
print '<tr class="oddeven">';
print '<td><label for="PAHEKO_PRODUCT_REF">'.$langs->trans('PAHEKO_PRODUCT_REF').'</label></td>';
print '<td><input type="text" name="PAHEKO_PRODUCT_REF" id="PAHEKO_PRODUCT_REF" value="'.getDolGlobalString('PAHEKO_PRODUCT_REF').'" class="minwidth200" placeholder="PAHEKO-INSTANCE"></td>';
print '<td>'.$langs->trans('PAHEKO_PRODUCT_REF_Desc').'</td>';
print '</tr>';

// Auto provisioning
print '<tr class="oddeven">';
print '<td><label for="PAHEKO_AUTO_PROVISIONING">'.$langs->trans('PAHEKO_AUTO_PROVISIONING').'</label></td>';
print '<td>';
print '<select name="PAHEKO_AUTO_PROVISIONING" id="PAHEKO_AUTO_PROVISIONING">';
print '<option value="1"'.(getDolGlobalString('PAHEKO_AUTO_PROVISIONING') ? ' selected' : '').'>'.$langs->trans('Yes').'</option>';
print '<option value="0"'.(!getDolGlobalString('PAHEKO_AUTO_PROVISIONING') ? ' selected' : '').'>'.$langs->trans('No').'</option>';
print '</select>';
print '</td>';
print '<td>'.$langs->trans('PAHEKO_AUTO_PROVISIONING_Desc').'</td>';
print '</tr>';

print '</table>';

print '<br>';
print '<div class="center">';
print '<button type="submit" name="button" class="button button-save">'.$langs->trans('Save').'</button>';
print '</div>';

print '</form>';

print '<br><br>';

// Bouton test
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="test">';
print '<div class="center">';
print '<button type="submit" name="button" class="button">'.$langs->trans('TestConfiguration').'</button>';
print '</div>';
print '</form>';

// Résultats test
if ($testResult) {
    print '<br>';
    print load_fiche_titre($langs->trans('TestResults'), '', '');

    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans('Status').'</td>';
    print '<td>'.$langs->trans('Message').'</td>';
    print '</tr>';

    foreach ($testResult['messages'] as $msg) {
        $class = $testResult['success'] ? 'oddeven' : 'oddeven opacitymedium';
        print '<tr class="'.$class.'">';
        print '<td>'.($testResult['success'] ? img_picto('', 'on', 'class="green"') : img_picto('', 'off', 'class="red"')).'</td>';
        print '<td>'.dol_escape_htmltag($msg).'</td>';
        print '</tr>';
    }

    print '</table>';
}

// Logs système
print '<br>';
print load_fiche_titre($langs->trans('SystemLogs'), '', '');

$sql = "SELECT l.rowid, l.event_type, l.message, l.created_at, i.instance_name, s.nom as soc_name";
$sql .= " FROM ".MAIN_DB_PREFIX."paheko_logs as l";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."paheko_instances as i ON l.fk_instance = i.rowid";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON i.fk_soc = s.rowid";
$sql .= " ORDER BY l.created_at DESC LIMIT 50";

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
