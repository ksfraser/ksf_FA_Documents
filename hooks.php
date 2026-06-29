<?php
/**
 * KSF FrontAccounting Module Hooks
 * 
 * STANDARD PATTERNS:
 * 
 * 1. ADDING MODULE TABS
 *    Define a class extending 'application' in hooks.php.
 *    Return new instance from install_tabs().
 *    Include add_extensions() to load other modules' install_options.
 * 
 * 2. ADDING MENU ITEMS TO EXISTING APPS
 *    Use install_options() with switch($app->id).
 *    Use add_module() + add_lapp_function() for new menu section.
 * 
 * 3. DATABASE SCHEMA
 *    DO NOT create tables in PHP code.
 *    Use sql/install.sql with @TB_PREF@ placeholders.
 *    Call $this->update_databases() in activate_extension().
 * 
 * 4. SECURITY
 *    Define SS_<MODULE> constant (section << 8).
 *    Define SA_<MODULE>VIEW and SA_<MODULE>MANAGE in install_access().
 * 
 * @package KsfFA_ksf_FA_Documents
 * @version 2.4.3
 */

define('SS_ksf_FA_Documents', 121 << 8);

$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

require_once __DIR__ . '/includes/events.inc';

class hooks_ksf_FA_Documents extends hooks {
    use \Ksfraser\Traits\HookQueryProviderTrait;

    var $module_name = 'ksf_FA_Documents';
    var $version = '1.0.0';

    /**
     * Add module tab
     * 
     * Return new application class instance to add a tab.
     * Omit or return nothing to skip tab addition.
     * 
     * @param application|null $app Ignored
     * @return application|null New tab application instance or nothing
     */
    function install_tabs($app) {
        // Override in modules that add apps
        // return new ksf_FA_Documents_app();
    }

    /**
     * Add menu items to existing FA applications
     * 
     * @param application $app FA application instance
     */
    function install_options($app) {
        // Override in modules that add menu items
    }

    /**
     * Define security areas
     * 
     * @return array [0] => $security_areas, [1] => $security_sections
     */
    function install_access() {
        $security_sections[SS_ksf_FA_Documents] = _("Documents");
        $security_areas['SA_DOCUMENTS_VIEW'] = array(SS_ksf_FA_Documents | 1, _("View documents"));
        $security_areas['SA_DOCUMENTS_MANAGE'] = array(SS_ksf_FA_Documents | 2, _("Manage documents"));
        return array($security_areas, $security_sections);
    }

    /**
     * Handle document_upload_attachment - fallback to legacy attachment storage
     * if ksf_FA_Attachments is not installed.
     */
    function document_upload_attachment($payload)
    {
        // If Attachments handled it (result is an array), skip
        if (is_array($payload) && isset($payload['attachment_id'])) {
            return null;
        }
        // Fallback: use legacy document_attachments table
        if (!empty($payload['file_path']) && !empty($payload['filename'])) {
            add_document_attachment(
                (int) ($payload['doc_id'] ?? $payload['entity_id'] ?? 0),
                $payload['file_path'],
                $payload['filename']
            );
        }
        return null;
    }

    /**
     * Activate extension
     * 
     * @param int $company Company number
     * @param bool $check_only Only check if activation possible
     * @return bool Success
     */
    function activate_extension($company, $check_only=true) {
        $this->ensure_composer_dependencies();
        $this->install_schema();
        return true;
    }

    protected function _getAdvertisedValues(): array
    {
        return [
            'documents.entity_types' => ['document', 'document_link'],
            'documents.events' => ['before_save', 'after_save', 'before_delete', 'after_delete', 'after_load'],
        ];
    }

    private function install_schema() {
        $sql_file = dirname(__FILE__) . '/sql/install.sql';
        if (!file_exists($sql_file)) {
            return;
        }

        $sql = file_get_contents($sql_file);
        if ($sql === false || $sql === '') {
            return;
        }

        $statements = explode(';', $sql);
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') {
                continue;
            }
            $lines = explode("\n", $stmt);
            $first_sql = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strncmp($line, '--', 2) === 0) {
                    continue;
                }
                $first_sql = $line;
                break;
            }
            $err_msg = preg_match('/^\s*(ALTER|INSERT)\s+/i', $first_sql)
                ? null
                : 'Could not execute ksf_FA_Documents schema statement';
            db_query($stmt, $err_msg);
        }
    }

    /**
     * Install composer dependencies if needed
     */
    private function ensure_composer_dependencies() {
        $module_dir = dirname(__FILE__);
        $autoload_path = $module_dir . '/vendor/autoload.php';
        
        if (file_exists($autoload_path)) {
            return;
        }
        
        $composer_path = $module_dir . '/composer.json';
        if (!file_exists($composer_path)) {
            return;
        }
        
        $prev_dir = getcwd();
        $ok = chdir($module_dir);
        if (!$ok) {
            error_log('ksf_FA_Documents: chdir failed to ' . $module_dir);
            return;
        }
        $output = array();
        $return_code = 0;
        exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
        chdir($prev_dir); // restore CWD
        if ($return_code !== 0) {
            error_log('ksf_FA_Documents: composer install failed: ' . implode("\n", $output));
        }
    }
}
