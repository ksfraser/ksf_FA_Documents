<?php
/**
 * Documents Module for FrontAccounting
 */

$module_id = 'Documents';

$module_version = '1.0.0';
$module_name = 'Documents Management';
$module_description = 'Document management with versioning, attachments, and acknowledgment tracking';

$module_tables = [
    'fa_documents',
    'fa_document_attachments',
    'fa_document_acknowledgments',
    'fa_document_versions',
];

$module_capabilities = [
    'SA_DOCUMENTVIEW' => 'View Documents',
    'SA_DOCUMENTCREATE' => 'Create Documents',
    'SA_DOCUMENTEDIT' => 'Edit Documents',
    'SA_DOCUMENTDELETE' => 'Delete Documents',
    'SA_DOCUMENTUPLOAD' => 'Upload Attachments',
];

function documents_install(): bool
{
    global $db, $db_multi_sql;
    
    $sql_file = dirname(__FILE__) . '/../sql/install.sql';
    
    if (!file_exists($sql_file)) {
        return false;
    }
    
    $sql = file_get_contents($sql_file);
    
    return $db_multi_sql($sql);
}

function documents_enable(): bool
{
    global $db;
    
    $sql = "UPDATE " . TB_PREF . "modules SET enabled = 1 WHERE name = 'Documents'";
    return $db->query($sql);
}

function documents_disable(): bool
{
    global $db;
    
    $sql = "UPDATE " . TB_PREF . "modules SET enabled = 0 WHERE name = 'Documents'";
    return $db->query($sql);
}

function documents_remove(): bool
{
    global $db, $db_multi_sql;
    
    $sql = "DROP TABLE IF EXISTS " . TB_PREF . "document_acknowledgments;
           DROP TABLE IF EXISTS " . TB_PREF . "document_versions;
           DROP TABLE IF EXISTS " . TB_PREF . "document_attachments;
           DELETE FROM " . TB_PREF . "modules WHERE name = 'Documents';";
    
    return $db_multi_sql($sql);
}

add_module($module_name, $module_version, $module_description);