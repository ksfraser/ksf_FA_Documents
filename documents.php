<?php
/**
 * Documents Module for FrontAccounting
 *
 * Document management with versioning, attachments, and acknowledgment tracking
 */

$module_version = '1.0.0';

$path_to_root = '../../..';

if (!defined('SA_DOCUMENTS')) {
    define('SA_DOCUMENTS', 10950);
}

$js = "";
if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(900, 600);
}

$new_page = $_SERVER['REQUEST_METHOD'] == 'GET';

if ($new_page) {
    include_once($path_to_root . "/includes/session.inc");
    include_once($path_to_root . "/includes/ui.inc");
    include_once($path_to_root . "/modules/FA_Documents/includes/documents_db.inc");
    include_once($path_to_root . "/modules/FA_Documents/includes/documents_ui.inc");
    
    page(_("Documents"), $page_security == 'SA_CUSTOMER', false, $js);
    
    if (isset($_GET['doc_id'])) {
        $doc_id = $_GET['doc_id'];
        
        if (isset($_GET['download'])) {
            $attachment_id = $_GET['download'];
            download_attachment($attachment_id);
        }
    }
}

function documents_menu($.selected = 'documents'): void
{
    global $path_to_root;
    
    $new_window = in_bounds('FA', 0, 1);
    
    menu_register([
        'documents' => [
            'title' => _('Documents'),
            'path' => $path_to_root . '/modules/FA_Documents/pages/documents.php',
            'new_window' => $new_window,
        ],
    ]);
}

function download_attachment(int $attachment_id): void
{
    global $db;
    
    $sql = "SELECT * FROM " . TB_PREF . "document_attachments WHERE id = " . intval($attachment_id);
    $result = $db->query($sql);
    
    if ($result && $db->num_rows($result) > 0) {
        $row = $db->fetch($result);
        
        $file_path = $row['file_path'];
        
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }
    
    display_error(_("File not found"));
}