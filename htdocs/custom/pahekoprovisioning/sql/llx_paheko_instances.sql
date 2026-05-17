-- Table: llx_paheko_instances
-- Stocke les instances Paheko provisionnées

CREATE TABLE IF NOT EXISTS llx_paheko_instances (
    rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
    fk_soc INTEGER NOT NULL,
    instance_name VARCHAR(255) NOT NULL,
    folder_path VARCHAR(500) NOT NULL,
    domain VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    suspended_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    UNIQUE KEY uk_paheko_instances_fk_soc (fk_soc),
    KEY idx_paheko_instances_status (status),
    KEY idx_paheko_instances_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
