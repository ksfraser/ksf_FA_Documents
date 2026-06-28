<?php
/**
 * ksf_FA_Documents Event Dispatching
 *
 * Dispatches CRUD lifecycle events via FA's hook_invoke with dual dispatch:
 * - before_* hooks use hook_invoke_first (filter: can modify/validate/abort)
 * - after_* hooks use hook_invoke_all (notification only)
 * - ksf_crud_event broadcast for Workflow integration
 *
 * Uses generic SuiteCRM-style hook names (before_save, after_save, etc.)
 * with entity_type and action in the payload.
 *
 * @package Ksfraser\Documents
 * @since 1.1.0
 */

/**
 * Dispatch a before_* hook (WordPress filter pattern via hook_invoke_first).
 *
 * @param string $hookName  e.g. 'before_save'
 * @param array  $data      Data to be modified/validated (includes entity_type, action)
 * @return array            Modified data
 */
function documents_before_hook($hookName, $data)
{
    if (function_exists('hook_invoke_first')) {
        $result = hook_invoke_first($hookName, $data);
        if (is_array($result)) {
            return $result;
        }
    }
    return $data;
}

/**
 * Dispatch an after_* hook (notification via hook_invoke_all).
 *
 * @param string $hookName    e.g. 'after_save'
 * @param int    $documentId
 * @param array  $data        Additional payload (includes entity_type, action)
 */
function documents_after_hook($hookName, $documentId, $data = [])
{
    $payload = array_merge([
        'event'       => $hookName,
        'module'      => 'documents',
        'entity_type' => 'document',
        'entity_id'   => $documentId,
        'timestamp'   => date('Y-m-d H:i:s'),
    ], $data);

    if (function_exists('hook_invoke_all')) {
        hook_invoke_all($hookName, $payload);
        hook_invoke_all('ksf_crud_event', $payload);
    }
}
