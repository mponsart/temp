-- Table: llx_paheko_logs
-- Stocke les logs de provisioning

CREATE TABLE IF NOT EXISTS llx_paheko_logs (
    rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
    fk_instance INTEGER NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_paheko_logs_fk_instance (fk_instance),
    KEY idx_paheko_logs_event_type (event_type),
    KEY idx_paheko_logs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
