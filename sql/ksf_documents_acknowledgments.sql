-- Documents module database schema for FrontAccounting
-- Table: 0_ksf_documents_acknowledgments

ALTER TABLE IF EXISTS `0_fa_document_acknowledgments` RENAME TO `0_ksf_documents_acknowledgments`;

CREATE TABLE IF NOT EXISTS `0_ksf_documents_acknowledgments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `employee_id` INT(11) NOT NULL,
    `acknowledged_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `doc_employee` (`doc_id`,`employee_id`),
    FOREIGN KEY (`doc_id`) REFERENCES `0_ksf_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
