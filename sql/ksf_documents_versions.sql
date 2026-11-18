-- Documents module database schema for FrontAccounting
-- Table: 0_ksf_documents_versions

ALTER TABLE IF EXISTS `0_fa_document_versions` RENAME TO `0_ksf_documents_versions`;

CREATE TABLE IF NOT EXISTS `0_ksf_documents_versions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `version` VARCHAR(20) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `doc_id` (`doc_id`),
    FOREIGN KEY (`doc_id`) REFERENCES `0_ksf_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
