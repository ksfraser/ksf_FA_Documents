-- Documents module database schema for FrontAccounting
-- v2.0.0: ACL columns on ksf_documents

ALTER TABLE `0_ksf_documents`
    ADD COLUMN `owner` INT NULL COMMENT 'FK to FA users.owner',
    ADD COLUMN `group_id` INT NULL COMMENT 'Access group ID for RBAC',
    ADD INDEX `idx_owner` (`owner`);
