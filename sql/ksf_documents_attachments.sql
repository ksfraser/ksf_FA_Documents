-- Documents module database schema for FrontAccounting
-- Table: 0_ksf_documents_attachments

ALTER TABLE IF EXISTS `0_fa_document_attachments` RENAME TO `0_ksf_documents_attachments`;

CREATE TABLE IF NOT EXISTS `0_ksf_documents_attachments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_size` INT(11) DEFAULT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `uploaded_by` INT(11) DEFAULT NULL,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `doc_id` (`doc_id`,`active`),
    FOREIGN KEY (`doc_id`) REFERENCES `0_ksf_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
