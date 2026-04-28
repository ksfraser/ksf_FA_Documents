<?php
/**
 * Documents Database Functions
 *
 * Database access functions for Documents module
 */

function create_document(string $title, string $type, ?string $expires_at, string $ack_required, ?string $ack_deadline, string $entity_type = '', ?int $entity_id = null): int
{
    global $db, $db_escape;
    
    $sql = "INSERT INTO " . TB_PREF . "documents 
        (title, type, status, created_by, created_at, expires_at, ack_required, ack_deadline, entity_type, entity_id)
        VALUES (
            " . $db_escape($title) . ",
            " . $db_escape($type) . ",
            'Active',
            " . (isset($_SESSION["wa_user"]) ? $_SESSION["wa_user"]->company : 'NULL') . ",
            NOW(),
            " . ($expires_at ? $db_escape($expires_at) : 'NULL') . ",
            " . $db_escape($ack_required) . ",
            " . ($ack_deadline ? $db_escape($ack_deadline) : 'NULL') . ",
            " . ($entity_type ? $db_escape($entity_type) : 'NULL') . ",
            " . ($entity_id ? intval($entity_id) : 'NULL') . "
        )";
    
    $db->query($sql);
    return $db->insert_id;
}

function get_document(int $id): ?array
{
    global $db;
    
    $sql = "SELECT * FROM " . TB_PREF . "documents WHERE id = " . intval($id);
    $result = $db->query($sql);
    
    if ($result && $db->num_rows($result) > 0) {
        return $db->fetch($result);
    }
    return null;
}

function update_document(int $id, string $title, string $type, ?string $expires_at, string $ack_required, ?string $ack_deadline, string $entity_type = '', ?int $entity_id = null): bool
{
    global $db, $db_escape;
    
    $sql = "UPDATE " . TB_PREF . "documents SET
        title = " . $db_escape($title) . ",
        type = " . $db_escape($type) . ",
        expires_at = " . ($expires_at ? $db_escape($expires_at) : 'NULL') . ",
        ack_required = " . $db_escape($ack_required) . ",
        ack_deadline = " . ($ack_deadline ? $db_escape($ack_deadline) : 'NULL') . ",
        entity_type = " . ($entity_type ? $db_escape($entity_type) : 'NULL') . ",
        entity_id = " . ($entity_id ? intval($entity_id) : 'NULL') . ",
        updated_at = NOW()
        WHERE id = " . intval($id);
    
    return $db->query($sql);
}

function get_documents_list(?string $type = null, string $search = '', int $limit = 100): ?object
{
    global $db;
    
    $sql = "SELECT * FROM " . TB_PREF . "documents WHERE status != 'Deleted'";
    
    if ($type) {
        $sql .= " AND type = " . $db->escape($type);
    }
    if ($search) {
        $sql .= " AND title LIKE " . $db->escape("%{$search}%");
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT " . intval($limit);
    
    return $db->query($sql);
}

function add_document_attachment(int $doc_id, string $file_path, string $file_name): bool
{
    global $db;
    
    $file_size = file_exists($file_path) ? filesize($file_path) : 0;
    $mime_type = mime_content_type($file_path);
    
    $sql = "INSERT INTO " . TB_PREF . "document_attachments 
        (doc_id, file_path, file_name, file_size, mime_type, uploaded_by, uploaded_at)
        VALUES (
            " . intval($doc_id) . ",
            " . $db->escape($file_path) . ",
            " . $db->escape($file_name) . ",
            " . intval($file_size) . ",
            " . $db->escape($mime_type) . ",
            " . (isset($_SESSION["wa_user"]) ? $_SESSION["wa_user"]->company : 'NULL') . ",
            NOW()
        )";
    
    return $db->query($sql);
}

function get_document_attachments(int $doc_id): ?object
{
    global $db;
    
    $sql = "SELECT * FROM " . TB_PREF . "document_attachments 
        WHERE doc_id = " . intval($doc_id) . " AND active = 1
        ORDER BY uploaded_at DESC";
    
    return $db->query($sql);
}

function delete_document_attachment(int $attachment_id): bool
{
    global $db;
    
    $sql = "UPDATE " . TB_PREF . "document_attachments SET active = 0
        WHERE id = " . intval($attachment_id);
    
    return $db->query($sql);
}

function create_acknowledgment(int $doc_id, int $user_id, int $employee_id): bool
{
    global $db;
    
    $sql = "INSERT INTO " . TB_PREF . "document_acknowledgments 
        (doc_id, user_id, employee_id, acknowledged_at)
        VALUES (
            " . intval($doc_id) . ",
            " . intval($user_id) . ",
            " . intval($employee_id) . ",
            NOW()
        )";
    
    return $db->query($sql);
}

function get_acknowledgments(int $employee_id): ?object
{
    global $db;
    
    $sql = "SELECT d.*, a.acknowledged_at FROM " . TB_PREF . "documents d
        INNER JOIN " . TB_PREF . "document_acknowledgments a ON d.id = a.doc_id
        WHERE a.employee_id = " . intval($employee_id) . "
        ORDER BY a.acknowledged_at DESC";
    
    return $db->query($sql);
}

function get_pending_acknowledgments(int $employee_id): ?object
{
    global $db;
    
    $now = date('Y-m-d');
    
    $sql = "SELECT d.* FROM " . TB_PREF . "documents d
        WHERE d.ack_required = 'yes'
        AND d.status = 'Active'
        AND (d.ack_deadline IS NULL OR d.ack_deadline >= " . $db->escape($now) . ")
        AND d.id NOT IN (
            SELECT doc_id FROM " . TB_PREF . "document_acknowledgments
            WHERE employee_id = " . intval($employee_id) . "
        )
        ORDER BY d.ack_deadline ASC";
    
    return $db->query($sql);
}

function get_expiring_documents(int $days = 30): ?object
{
    global $db;
    
    $futureDate = date('Y-m-d', strtotime("+{$days} days"));
    
    $sql = "SELECT * FROM " . TB_PREF . "documents
        WHERE expires_at IS NOT NULL 
        AND expires_at <= " . $db->escape($futureDate) . "
        AND status = 'Active'
        ORDER BY expires_at ASC";
    
    return $db->query($sql);
}