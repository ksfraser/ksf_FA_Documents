-- Documents module database schema for FrontAccounting

-- Main documents table
CREATE TABLE IF NOT EXISTS `fa_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `type` ENUM('Policy','Contract','Form','Training','Handbook','Other') NOT NULL DEFAULT 'Other',
    `status` ENUM('Active','Archived','Deleted') NOT NULL DEFAULT 'Active',
    `created_by` INT(11) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `expires_at` DATE DEFAULT NULL,
    `ack_required` ENUM('yes','no') NOT NULL DEFAULT 'no',
    `ack_deadline` DATE DEFAULT NULL,
    `entity_type` VARCHAR(50) DEFAULT NULL,
    `entity_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `type` (`type`),
    KEY `status` (`status`),
    KEY `entity` (`entity_type`,`entity_id`),
    KEY `expires_at` (`expires_at`),
    KEY `ack_required` (`ack_required`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document attachments (FA native)
CREATE TABLE IF NOT EXISTS `fa_document_attachments` (
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
    FOREIGN KEY (`doc_id`) REFERENCES `fa_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document acknowledgments
CREATE TABLE IF NOT EXISTS `fa_document_acknowledgments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `employee_id` INT(11) NOT NULL,
    `acknowledged_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `doc_employee` (`doc_id`,`employee_id`),
    FOREIGN KEY (`doc_id`) REFERENCES `fa_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document versions
CREATE TABLE IF NOT EXISTS `fa_document_versions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `version` VARCHAR(20) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `doc_id` (`doc_id`),
    FOREIGN KEY (`doc_id`) REFERENCES `fa_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Module version
INSERT INTO `fa_modules` (`name`, `version`, `enabled`, `installed`) VALUES
('Documents', '1.0.0', 1, NOW())
ON DUPLICATE KEY UPDATE `version` = '1.0.0', `installed` = NOW();

-- ============================================================
-- v2.0.0: Multi-entity links, ACL, shared attachments
-- ============================================================

-- Add ACL columns to fa_documents
ALTER TABLE `@TB_PREF@fa_documents`
    ADD COLUMN `owner` INT NULL COMMENT 'FK to FA users.owner',
    ADD COLUMN `group_id` INT NULL COMMENT 'Access group ID for RBAC',
    ADD INDEX `idx_owner` (`owner`);

-- Multi-entity link table (replaces old entity_type/entity_id columns)
CREATE TABLE IF NOT EXISTS `@TB_PREF@fa_document_links` (
    `id`            INT           NOT NULL AUTO_INCREMENT,
    `doc_id`        INT           NOT NULL COMMENT 'FK to fa_documents.id',
    `entity_type`   VARCHAR(64)   NOT NULL COMMENT 'Entity type (debtor, employee, project, etc.)',
    `entity_id`     INT           NOT NULL COMMENT 'FK to entity record',
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_doc` (`doc_id`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    UNIQUE KEY `uq_doc_entity` (`doc_id`, `entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Multi-entity links for documents';

-- Migration data: copy existing entity_type/entity_id to fa_document_links
INSERT INTO `@TB_PREF@fa_document_links` (doc_id, entity_type, entity_id, created_at)
    SELECT id, entity_type, entity_id, created_at
    FROM `@TB_PREF@fa_documents`
    WHERE entity_type IS NOT NULL AND entity_id IS NOT NULL
    AND entity_type != '' AND entity_id != 0
ON DUPLICATE KEY UPDATE id = id;