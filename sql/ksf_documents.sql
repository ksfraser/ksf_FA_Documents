-- Documents module database schema for FrontAccounting
-- Table: 0_ksf_documents

ALTER TABLE IF EXISTS `0_fa_documents` RENAME TO `0_ksf_documents`;

CREATE TABLE IF NOT EXISTS `0_ksf_documents` (
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
