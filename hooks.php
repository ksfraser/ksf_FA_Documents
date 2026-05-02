<?php
/**
 * FA_Documents Module Hooks for FrontAccounting
 */

define('SS_DOCUMENTS', 121 << 8);

class hooks_fa_documents extends hooks {
    var $module_name = 'fa_documents';

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'Documents':
                $app->add_lapp_function(0, _("Documents"),
                    $path_to_root."/modules/".$this->module_name."/documents.php", 'SA_DOCUMENTVIEW', MENU_ENTRY);
                $app->add_lapp_function(1, _("Upload Attachment"),
                    $path_to_root."/modules/".$this->module_name."/upload.php", 'SA_DOCUMENTUPLOAD', MENU_ENTRY);
                $app->add_lapp_function(2, _("Acknowledgments"),
                    $path_to_root."/modules/".$this->module_name."/acknowledgments.php", 'SA_DOCUMENTVIEW', MENU_INQUIRY);
                $app->add_rapp_function(3, _("Document Versions"),
                    $path_to_root."/modules/".$this->module_name."/versions.php", 'SA_DOCUMENTEDIT', MENU_MAINTENANCE);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_DOCUMENTS] = _("Documents Management");
        $security_areas['SA_DOCUMENTVIEW'] = array(SS_DOCUMENTS | 1, _("View Documents"));
        $security_areas['SA_DOCUMENTCREATE'] = array(SS_DOCUMENTS | 2, _("Create Documents"));
        $security_areas['SA_DOCUMENTEDIT'] = array(SS_DOCUMENTS | 3, _("Edit Documents"));
        $security_areas['SA_DOCUMENTDELETE'] = array(SS_DOCUMENTS | 4, _("Delete Documents"));
        $security_areas['SA_DOCUMENTUPLOAD'] = array(SS_DOCUMENTS | 5, _("Upload Attachments"));
        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only=true) {
        $updates = array('sql/update.sql' => array($this->module_name));
        $ok = $this->update_databases($company, $updates, $check_only);
        if ($check_only || !$ok) {
            return $ok;
        }
        $this->ensure_documents_schema();
        return $ok;
    }

    private function table_exists($table) {
        $sql = "SHOW TABLES LIKE " . db_escape($table);
        $res = db_query($sql, 'Failed checking table existence');
        return db_num_rows($res) > 0;
    }

    private function ensure_documents_schema() {
        $tables = array(
            TB_PREF . "fa_documents" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_documents` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `title` VARCHAR(255) NOT NULL,
                    `description` TEXT,
                    `document_type` VARCHAR(50) DEFAULT 'General',
                    `file_path` VARCHAR(500) DEFAULT NULL,
                    `file_size` INT(11) DEFAULT 0,
                    `version` INT(11) DEFAULT 1,
                    `entity_type` VARCHAR(20) DEFAULT NULL,
                    `entity_id` VARCHAR(20) DEFAULT NULL,
                    `created_by` VARCHAR(100) DEFAULT NULL,
                    `status` VARCHAR(20) DEFAULT 'Active',
                    `inactive` TINYINT(1) DEFAULT 0,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_entity` (`entity_type`, `entity_id`),
                    KEY `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_document_attachments" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_document_attachments` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `document_id` INT(11) NOT NULL,
                    `file_name` VARCHAR(255) NOT NULL,
                    `file_path` VARCHAR(500) NOT NULL,
                    `file_size` INT(11) DEFAULT 0,
                    `mime_type` VARCHAR(100) DEFAULT NULL,
                    `uploaded_by` VARCHAR(100) DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_document` (`document_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_document_acknowledgments" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_document_acknowledgments` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `document_id` INT(11) NOT NULL,
                    `user_id` VARCHAR(100) NOT NULL,
                    `acknowledged_at` DATETIME NOT NULL,
                    `ip_address` VARCHAR(45) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_doc_user` (`document_id`, `user_id`),
                    KEY `idx_user` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_document_versions" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_document_versions` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `document_id` INT(11) NOT NULL,
                    `version_number` INT(11) NOT NULL,
                    `file_path` VARCHAR(500) NOT NULL,
                    `change_summary` TEXT,
                    `created_by` VARCHAR(100) DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_document` (`document_id`),
                    KEY `idx_version` (`version_number`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $table_name => $sql) {
            db_query($sql, "Could not create Documents table: $table_name");
        }
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if needed
    }
}
?>
