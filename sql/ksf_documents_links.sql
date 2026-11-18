-- Documents module database schema for FrontAccounting
-- Table: 0_ksf_documents_links

ALTER TABLE IF EXISTS `0_fa_document_links` RENAME TO `0_ksf_documents_links`;

CREATE TABLE IF NOT EXISTS `0_ksf_documents_links` (
    `id`            INT           NOT NULL AUTO_INCREMENT,
    `doc_id`        INT           NOT NULL COMMENT 'FK to ksf_documents.id',
    `entity_type`   VARCHAR(64)   NOT NULL COMMENT 'Entity type (debtor, employee, project, etc.)',
    `entity_id`     INT           NOT NULL COMMENT 'FK to entity record',
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_doc` (`doc_id`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    UNIQUE KEY `uq_doc_entity` (`doc_id`, `entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Multi-entity links for documents';

-- Migration data: copy existing entity_type/entity_id to ksf_documents_links
INSERT INTO `0_ksf_documents_links` (doc_id, entity_type, entity_id, created_at)
    SELECT id, entity_type, entity_id, created_at
    FROM `0_ksf_documents`
    WHERE entity_type IS NOT NULL AND entity_id IS NOT NULL
    AND entity_type != '' AND entity_id != 0
ON DUPLICATE KEY UPDATE id = id;
