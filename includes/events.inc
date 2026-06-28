<?php
/**
 * ksf_FA_Documents Event Dispatching
 *
 * Dispatches CRUD lifecycle events via FA's hook_invoke_all with dual dispatch:
 * 1. Specific event: <event_name> (e.g. 'document_created')
 * 2. Broadcast event: 'ksf_crud_event' (all modules see all events)
 *
 * @package Ksfraser\Documents
 * @since 1.1.0
 */

/**
 * Dispatch a document lifecycle event.
 *
 * @param string $eventName   Specific event name (e.g. 'document_created')
 * @param int    $documentId  The affected document ID
 * @param array  $data        Additional payload data
 */
function documents_dispatch_event($eventName, $documentId, $data = [])
{
    $payload = array_merge([
        'event'       => $eventName,
        'module'      => 'documents',
        'record_type' => 'document',
        'record_id'   => $documentId,
        'timestamp'   => date('Y-m-d H:i:s'),
    ], $data);

    if (function_exists('hook_invoke_all')) {
        hook_invoke_all($eventName, $payload);
        hook_invoke_all('ksf_crud_event', $payload);
    }
}
