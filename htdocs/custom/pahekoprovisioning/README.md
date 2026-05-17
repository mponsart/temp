# Module Dolibarr - Paheko Provisioning

Module de provisioning automatique d'instances Paheko pour Dolibarr.

## Description

Ce module permet de créer, suspendre et supprimer automatiquement des instances Paheko en fonction des événements Dolibarr :
- Paiement de facture → Création d'instance
- Facture impayée → Suspension d'instance
- Suppression tiers → Suppression d'instance

## Prérequis

- Dolibarr 20+ / 21+ / 23+
- PHP 8.3+
- MySQL / MariaDB
- Hébergement mutualisé cPanel ou serveur dédié
- Template Paheko fonctionnel

## Installation

### 1. Copier le module

```bash
cp -r pahekoprovisioning /chemin/vers/dolibarr/htdocs/custom/
```

### 2. Définir les permissions

```bash
chown -R www-data:www-data /chemin/vers/dolibarr/htdocs/custom/pahekoprovisioning
chmod -R 755 /chemin/vers/dolibarr/htdocs/custom/pahekoprovisioning
```

### 3. Activer le module

1. Connectez-vous à Dolibarr en tant qu'administrateur
2. Allez dans `Accueil > Configuration > Modules`
3. Cherchez "Paheko Provisioning"
4. Cliquez sur l'icône pour activer le module

### 4. Configurer le module

1. Allez dans `Paheko Provisioning > Configuration`
2. Définissez le chemin du template Paheko (ex: `/home/user/paheko-template`)
3. Définissez le chemin de stockage des instances (ex: `/home/user/paheko-clients`)
4. Activez le provisioning automatique
5. Cliquez sur "Tester la configuration" pour vérifier

### 5. Créer les tables

Les tables sont créées automatiquement à l'activation du module.

Vérification manuelle :
```sql
SHOW TABLES LIKE 'llx_paheko_%';
```

## Configuration requise

### Template Paheko

Le dossier template doit contenir une installation complète de Paheko :
```
/home/user/paheko-template/
├── index.php
├── documents/
├── htdocs/
├── custom/
└── ...
```

### Permissions

Le processus PHP de Dolibarr doit avoir :
- Lecture sur le dossier template
- Écriture sur le dossier des instances

### Variables d'environnement

Aucune variable d'environnement n'est requise. Tout est configuré depuis l'interface Dolibarr.

## Utilisation

### Création automatique

Lorsqu'une facture est marquée comme payée dans Dolibarr :
1. Le module détecte l'événement `BILLING_INVOICE_PAID`
2. Il crée une instance Paheko pour le tiers
3. Il copie le template dans `/home/user/paheko-clients/client_XXX_YYYYMMDDHHMMSS/`
4. Il génère un `config.local.php` unique
5. Il log l'événement dans `llx_paheko_logs`

### Suspension

Lorsqu'une facture est impayée :
1. Le module détecte l'événement `BILLING_INVOICE_UNPAID`
2. Il crée un fichier `SUSPENDED` dans le dossier de l'instance
3. Paheko doit vérifier ce fichier pour bloquer l'accès
4. Le statut est mis à jour dans `llx_paheko_instances`

### Réactivation

Le cron job horaire ou la réactivation manuelle :
1. Supprime le fichier `SUSPENDED`
2. Met à jour le statut en `active`

### Suppression

Lors de la suppression d'un tiers :
1. Le module détecte `THIRDPARTY_DELETE`
2. Il supprime récursivement le dossier de l'instance
3. Il marque l'instance comme `deleted` dans la BDD

## Pages du module

### Dashboard
`/custom/pahekoprovisioning/admin/index.php`
- Statistiques des instances
- Logs récents
- Liens rapides

### Liste des instances
`/custom/pahekoprovisioning/instances/list.php`
- Liste toutes les instances
- Filtres par statut
- Actions : suspendre, réactiver, supprimer

### Configuration
`/custom/pahekoprovisioning/admin/setup.php`
- Configuration des chemins
- Test de configuration
- Logs système

## Base de données

### Table `llx_paheko_instances`

| Champ | Type | Description |
|-------|------|-------------|
| rowid | int | ID unique |
| fk_soc | int | ID du tiers Dolibarr |
| instance_name | varchar | Nom de l'instance |
| folder_path | varchar | Chemin absolu du dossier |
| domain | varchar | Domaine (optionnel) |
| status | varchar | active/suspended/deleted |
| created_at | datetime | Date de création |
| suspended_at | datetime | Date de suspension |
| deleted_at | datetime | Date de suppression |

### Table `llx_paheko_logs`

| Champ | Type | Description |
|-------|------|-------------|
| rowid | int | ID unique |
| fk_instance | int | ID de l'instance |
| event_type | varchar | CREATE/SUSPEND/UNSUSPEND/DELETE |
| message | text | Message de log |
| created_at | datetime | Date de l'événement |

## Cron job

Un cron job est configuré pour s'exécuter toutes les heures :
- Vérifie la cohérence des instances
- Suspend les instances avec factures impayées
- Réactive les instances payées

Pour activer le cron :
1. Allez dans `Accueil > Outils > Cron jobs`
2. Cherchez "Synchronisation instances Paheko"
3. Activez-le et définissez la fréquence

## Sécurité

- Vérification des permissions Dolibarr
- Protection CSRF sur toutes les actions
- Logs de tous les événements
- Aucun accès public aux dossiers d'instances
- Mots de passe générés de façon sécurisée

## Dépannage

### Le module ne crée pas d'instances

1. Vérifiez que le module est activé
2. Vérifiez que `PAHEKO_AUTO_PROVISIONING = 1`
3. Vérifiez les permissions du dossier template
4. Consultez les logs dans `llx_paheko_logs`

### Erreur de permissions

Assurez-vous que l'utilisateur web a accès en écriture :
```bash
ls -la /home/user/paheko-clients/
chown www-data:www-data /home/user/paheko-clients/
chmod 755 /home/user/paheko-clients/
```

### Template non trouvé

Vérifiez le chemin dans la configuration :
- Doit être un chemin absolu
- Doit se terminer sans slash
- Doit être accessible en lecture

## Support

Pour toute question ou problème, consultez la documentation Dolibarr :
https://wiki.dolibarr.org/index.php/Module_development

## Licence

GPL v3

## Version

1.0.0
