<?php
namespace App\Services;

use App\Models\Instance;
use Illuminate\Support\Facades\Log;

class PahekoInstanceService
{
    public function deploy(Instance $instance)
    {
        $cpanelUrl = env('CPANEL_API_URL');
        $cpanelUser = env('CPANEL_API_USER');
        $cpanelToken = env('CPANEL_API_TOKEN');
        $usersPath = env('CPANEL_USERS_PATH', '/home/gowo3083/app.monasso.eu/users');
        $subdomain = $instance->subdomain;
        $targetDir = rtrim($usersPath, '/') . '/' . $subdomain;

        if ($cpanelUrl && $cpanelUser && $cpanelToken) {
            try {
                // Création du dossier via UAPI
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'cpanel ' . $cpanelUser . ':' . $cpanelToken,
                ])->post($cpanelUrl . '/execute/Fileman/mkdir', [
                    'path' => $targetDir,
                    'permissions' => '0755',
                ]);
                if (!$response->ok()) {
                    Log::error('cPanel UAPI: Erreur création dossier', ['response' => $response->body()]);
                    return false;
                }
                // Création d'un index.html minimal via UAPI
                $indexContent = "<html><head><title>Bienvenue sur MonAsso</title></head><body><h1>Espace prêt pour " . htmlspecialchars($instance->association_name) . "</h1></body></html>";
                $indexPath = $targetDir . '/index.html';
                $response2 = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'cpanel ' . $cpanelUser . ':' . $cpanelToken,
                ])->post($cpanelUrl . '/execute/Fileman/save_file', [
                    'file' => $indexPath,
                    'data' => $indexContent,
                    'encoding' => 'utf-8',
                ]);
                if (!$response2->ok()) {
                    Log::error('cPanel UAPI: Erreur création index.html', ['response' => $response2->body()]);
                    return false;
                }
                Log::info('cPanel UAPI: Instance déployée pour ' . $subdomain);
                return true;
            } catch (\Throwable $e) {
                Log::error('cPanel UAPI: Exception déploiement - ' . $e->getMessage());
                return false;
            }
        } else {
            Log::error('cPanel UAPI: Variables d\'environnement manquantes');
            return false;
        }
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
