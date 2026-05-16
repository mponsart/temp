<?php
namespace App\Services;

use App\Models\Instance;
use Illuminate\Support\Facades\Log;

class PahekoInstanceService
{
    public function deploy(Instance $instance)
    {
        // Paramètres FTP à configurer dans .env
        $ftpHost = config('services.ftp.host');
        $ftpUser = config('services.ftp.user');
        $ftpPass = config('services.ftp.pass');
        $ftpBasePath = config('services.ftp.base_path', '/');
        $subdomain = $instance->subdomain;
        $remotePath = rtrim($ftpBasePath, '/') . '/' . $subdomain;

        // Connexion FTP
        $conn = ftp_connect($ftpHost);
        if (!$conn) {
            Log::error('FTP: Impossible de se connecter à ' . $ftpHost);
            return false;
        }
        if (!ftp_login($conn, $ftpUser, $ftpPass)) {
            Log::error('FTP: Authentification échouée');
            ftp_close($conn);
            return false;
        }
        ftp_pasv($conn, true);
        // Création du dossier
        if (!@ftp_mkdir($conn, $remotePath)) {
            Log::warning('FTP: Dossier déjà existant ou erreur de création pour ' . $remotePath);
        }
        // Déploiement du template Paheko (à adapter selon ton archive ou structure)
        // Exemple: upload d'un index.html minimal
        $localFile = base_path('resources/views/welcome.blade.php');
        $remoteFile = $remotePath . '/index.html';
        ftp_put($conn, $remoteFile, $localFile, FTP_ASCII);
        ftp_close($conn);
        Log::info('FTP: Instance déployée pour ' . $subdomain);
        return true;
    }

    public function suspend(Instance $instance)
    {
        // Ici, tu peux renommer le dossier, placer un .maintenance, ou supprimer l'accès
        // À adapter selon ta politique de suspension
        Log::info('Instance suspendue: ' . $instance->subdomain);
    }

    public function unsuspend(Instance $instance)
    {
        // Inverse de suspend()
        Log::info('Instance réactivée: ' . $instance->subdomain);
    }

    public function delete(Instance $instance)
    {
        $ftpHost = config('services.ftp.host');
        $ftpUser = config('services.ftp.user');
        $ftpPass = config('services.ftp.pass');
        $ftpBasePath = config('services.ftp.base_path', '/');
        $subdomain = $instance->subdomain;
        $remotePath = rtrim($ftpBasePath, '/') . '/' . $subdomain;

        $conn = ftp_connect($ftpHost);
        if (!$conn) {
            Log::error('FTP: Impossible de se connecter à ' . $ftpHost);
            return false;
        }
        if (!ftp_login($conn, $ftpUser, $ftpPass)) {
            Log::error('FTP: Authentification échouée');
            ftp_close($conn);
            return false;
        }
        ftp_pasv($conn, true);
        // Suppression récursive du dossier (attention, irréversible)
        $this->ftpDeleteRecursive($conn, $remotePath);
        ftp_close($conn);
        Log::info('FTP: Instance supprimée pour ' . $subdomain);
        return true;
    }

    private function ftpDeleteRecursive($conn, $path)
    {
        $files = @ftp_nlist($conn, $path);
        if ($files) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                if (@ftp_chdir($conn, $file)) {
                    ftp_chdir($conn, '..');
                    $this->ftpDeleteRecursive($conn, $file);
                } else {
                    @ftp_delete($conn, $file);
                }
            }
        }
        @ftp_rmdir($conn, $path);
    }
}
