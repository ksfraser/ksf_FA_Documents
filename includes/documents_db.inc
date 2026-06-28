<?php
/**
 * Documents Database Functions
 *
 * Database access functions for Documents module
 */

function create_document(string $title, string $type, ?string $expires_at, string $ack_required, ?string $ack_deadline, string $entity_type = '', ?int $entity_id = null, ?int $owner = null, ?int $group_id = null): int
{
    global $db, $db_escape;
    
    $hookData = documents_before_hook('before_save', array_merge(['entity_type' => 'document', 'action' => 'create'], [
        'title'        => $title,
        'type'         => $type,
        'expires_at'   => $expires_at,
        'ack_required' => $ack_required,
        'ack_deadline' => $ack_deadline,
        'entity_type'  => $entity_type,
        'entity_id'    => $entity_id,
        'owner'        => $owner,
        'group_id'     => $group_id,
    ]));
    $title = $hookData['title'];
    $type = $hookData['type'];
    $expires_at = $hookData['expires_at'];
    $ack_required = $hookData['ack_required'];
    $ack_deadline = $hookData['ack_deadline'];
    $entity_type = $hookData['entity_type'];
    $entity_id = $hookData['entity_id'];
    $owner = $hookData['owner'];
    $group_id = $hookData['group_id'];
    
    $sql = "INSERT INTO " . TB_PREF . "documents 
        (title, type, status, created_by, created_at, expires_at, ack_required, ack_deadline, entity_type, entity_id, owner, group_id)
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
            " . ($entity_id ? intval($entity_id) : 'NULL') . ",
            " . ($owner !== null ? intval($owner) : 'NULL') . ",
            " . ($group_id !== null ? intval($group_id) : 'NULL') . "
        )";
    
    $db->query($sql);
    $docId = $db->insert_id;
    
    if (function_exists('documents_after_hook')) {
        documents_after_hook('after_save', $docId, ['entity_type' => 'document', 'action' => 'create', 'data' => [
            'id'           => $docId,
            'title'        => $title,
            'type'         => $type,
            'expires_at'   => $expires_at,
            'ack_required' => $ack_required,
            'ack_deadline' => $ack_deadline,
            'entity_type'  => $entity_type,
            'entity_id'    => $entity_id,
            'owner'        => $owner,
            'group_id'     => $group_id,
        ]]);
    }
    
    return $docId;
}

function get_document(int $id): ?array
{
    global $db;
    
    $sql = "SELECT * FROM " . TB_PREF . "documents WHERE id = " . intval($id);
    $result = $db->query($sql);
    
    if ($result && $db->num_rows($result) > 0) {
        $row = $db->fetch($result);
        if (function_exists('hook_invoke_first')) {
            $payload = ['entity_type' => 'document', 'action' => 'load', 'data' => $row];
            $modified = hook_invoke_first('after_load', $payload);
            $row = is_array($modified) ? ($modified['data'] ?? $modified) : $row;
        }
        return $row;
    }
    return null;
}

function update_document(int $id, string $title, string $type, ?string $expires_at, string $ack_required, ?string $ack_deadline, string $entity_type = '', ?int $entity_id = null, ?int $owner = null, ?int $group_id = null): bool
{
    global $db, $db_escape;
    
    $hookData = documents_before_hook('before_save', array_merge(['entity_type' => 'document', 'action' => 'update'], [
        'doc_id'       => $id,
        'title'        => $title,
        'type'         => $type,
        'expires_at'   => $expires_at,
        'ack_required' => $ack_required,
        'ack_deadline' => $ack_deadline,
        'entity_type'  => $entity_type,
        'entity_id'    => $entity_id,
        'owner'        => $owner,
        'group_id'     => $group_id,
    ]));
    $title = $hookData['title'];
    $type = $hookData['type'];
    $expires_at = $hookData['expires_at'];
    $ack_required = $hookData['ack_required'];
    $ack_deadline = $hookData['ack_deadline'];
    $entity_type = $hookData['entity_type'];
    $entity_id = $hookData['entity_id'];
    $owner = $hookData['owner'];
    $group_id = $hookData['group_id'];
    
    $sql = "UPDATE " . TB_PREF . "documents SET
        title = " . $db_escape($title) . ",
        type = " . $db_escape($type) . ",
        expires_at = " . ($expires_at ? $db_escape($expires_at) : 'NULL') . ",
        ack_required = " . $db_escape($ack_required) . ",
        ack_deadline = " . ($ack_deadline ? $db_escape($ack_deadline) : 'NULL') . ",
        entity_type = " . ($entity_type ? $db_escape($entity_type) : 'NULL') . ",
        entity_id = " . ($entity_id ? intval($entity_id) : 'NULL') . ",
        owner = " . ($owner !== null ? intval($owner) : 'NULL') . ",
        group_id = " . ($group_id !== null ? intval($group_id) : 'NULL') . ",
        updated_at = NOW()
        WHERE id = " . intval($id);
    
    $result = $db->query($sql);
    
    if ($result && function_exists('documents_after_hook')) {
        documents_after_hook('after_save', $id, ['entity_type' => 'document', 'action' => 'update', 'data' => [
            'id'           => $id,
            'title'        => $title,
            'type'         => $type,
            'expires_at'   => $expires_at,
            'ack_required' => $ack_required,
            'ack_deadline' => $ack_deadline,
            'entity_type'  => $entity_type,
            'entity_id'    => $entity_id,
            'owner'        => $owner,
            'group_id'     => $group_id,
        ]]);
    }
    
    return $result;
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

/**
 * Link a document to an entity (multi-entity support)
 *
 * @param int    $doc_id
 * @param string $entity_type
 * @param int    $entity_id
 * @return int   Link ID
 * @throws RuntimeException If document not found
 */
function link_document(int $doc_id, string $entity_type, int $entity_id): int
{
    $doc = get_document($doc_id);
    if (!$doc) {
        throw new \RuntimeException("Document {$doc_id} not found");
    }
    
    documents_before_hook('before_save', array_merge(['entity_type' => 'document_link', 'action' => 'create'], [
        'doc_id'      => $doc_id,
        'entity_type' => $entity_type,
        'entity_id'   => $entity_id,
    ]));
    
    global $db;
    $sql = "INSERT IGNORE INTO " . TB_PREF . "fa_document_links
        (doc_id, entity_type, entity_id)
        VALUES (" . intval($doc_id) . ",
                " . $db->escape($entity_type) . ",
                " . intval($entity_id) . ")";
    
    $db->query($sql);
    $linkId = $db->insert_id;
    
    if (function_exists('documents_after_hook')) {
        documents_after_hook('after_save', $doc_id, ['entity_type' => 'document_link', 'action' => 'create', 'linked_entity_type' => $entity_type, 'linked_entity_id' => $entity_id, 'link_id' => $linkId]);
    }
    
    return $linkId;
}

/**
 * Unlink a document from an entity
 */
function unlink_document(int $doc_id, string $entity_type, int $entity_id): void
{
    documents_before_hook('before_delete', array_merge(['entity_type' => 'document_link'], [
        'doc_id'      => $doc_id,
        'entity_type' => $entity_type,
        'entity_id'   => $entity_id,
    ]));
    
    global $db;
    $sql = "DELETE FROM " . TB_PREF . "fa_document_links
        WHERE doc_id = " . intval($doc_id) . "
        AND entity_type = " . $db->escape($entity_type) . "
        AND entity_id = " . intval($entity_id);
    
    $db->query($sql);
    
    if (function_exists('documents_after_hook')) {
        documents_after_hook('after_delete', $doc_id, ['entity_type' => 'document_link', 'linked_entity_type' => $entity_type, 'linked_entity_id' => $entity_id]);
    }
}

/**
 * Get all entities linked to a document
 *
 * @param int $doc_id
 * @return array
 */
function get_document_linked_entities(int $doc_id): array
{
    global $db;
    
    $sql = "SELECT entity_type, entity_id FROM " . TB_PREF . "fa_document_links
        WHERE doc_id = " . intval($doc_id);
    
    $result = $db->query($sql);
    $entities = [];
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch($result)) {
            $entities[] = [
                'entity_type' => $row['entity_type'],
                'entity_id'   => (int) $row['entity_id'],
            ];
        }
    }
    return $entities;
}

/**
 * ACL: Check if a user can view a document
 */
function document_can_view(int $doc_id, ?int $user_id = null): bool
{
    if ($user_id === null) {
        $user_id = (int) ($_SESSION['wa_current_user']->user ?? 0);
    }
    
    $doc = get_document($doc_id);
    if (!$doc) {
        return false;
    }
    
    if (!empty($doc['owner']) && (int) $doc['owner'] === $user_id) {
        return true;
    }
    
    if (empty($doc['owner'])) {
        return true;
    }
    
    return false;
}

/**
 * ACL: Check if a user can edit a document
 */
function document_can_edit(int $doc_id, ?int $user_id = null): bool
{
    if ($user_id === null) {
        $user_id = (int) ($_SESSION['wa_current_user']->user ?? 0);
    }
    
    $doc = get_document($doc_id);
    if (!$doc) {
        return false;
    }
    
    if (!empty($doc['owner']) && (int) $doc['owner'] === $user_id) {
        return true;
    }
    
    return false;
}

/**
 * Upload attachment via shared fa_attachments service
 * Delegates to ksf_FA_Attachments module
 *
 * @param int    $doc_id
 * @param string $source_file   Path to the uploaded file
 * @param string $original_name Original filename
 * @return array|false
 */
function add_document_shared_attachment(int $doc_id, string $source_file, string $original_name)
{
    $hookData = [];
    if (function_exists('documents_before_hook')) {
        $hookData = documents_before_hook('before_save', array_merge(['entity_type' => 'document', 'action' => 'attach'], [
            'doc_id'        => $doc_id,
            'source_file'   => $source_file,
            'original_name' => $original_name,
        ]));
    }
    
    // Try via hook first - ksf_FA_Attachments listens for this
    if (function_exists('hook_invoke_first')) {
        $result = hook_invoke_first('document_upload_attachment', [
            'doc_id'      => $doc_id,
            'entity_type' => 'document',
            'entity_id'   => $doc_id,
            'filename'    => $hookData['original_name'] ?? $original_name,
            'file_path'   => $hookData['source_file'] ?? $source_file,
            'file_size'   => file_exists($hookData['source_file'] ?? $source_file) ? filesize($hookData['source_file'] ?? $source_file) : 0,
            'mime_type'   => mime_content_type($hookData['source_file'] ?? $source_file) ?: 'application/octet-stream',
            'uploaded_by' => (string) ($_SESSION['wa_current_user']->user ?? 'system'),
        ]);
        if ($result !== null) {
            if (function_exists('documents_after_hook')) {
                $attachData = [
                    'doc_id'        => $doc_id,
                    'source_file'   => $hookData['source_file'] ?? $source_file,
                    'original_name' => $hookData['original_name'] ?? $original_name,
                ];
                if (isset($result['id'])) {
                    $attachData['attachment_id'] = $result['id'];
                }
                documents_after_hook('after_save', $doc_id, ['entity_type' => 'document', 'action' => 'attach', 'data' => $attachData]);
            }
            return $result;
        }
    }
    
    // Fallback: legacy fa_document_attachments
    return add_document_attachment($doc_id, $hookData['source_file'] ?? $source_file, $hookData['original_name'] ?? $original_name);
}

/**
 * Get shared attachments for a document
 */
function get_document_shared_attachments(int $doc_id): array
{
    // Try via hook first
    if (function_exists('hook_invoke_first')) {
        $result = hook_invoke_first('document_get_attachments', [
            'doc_id'      => $doc_id,
            'entity_type' => 'document',
            'entity_id'   => $doc_id,
        ]);
        if ($result !== null) {
            return $result;
        }
    }
    return [];
}