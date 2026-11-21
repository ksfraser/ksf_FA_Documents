<?php
declare(strict_types=1);

/**
 * ksf_FA_Documents - FrontAccounting Module
 *
 * FA adapter over ksfraser/ksf-documents standalone business logic.
 *
 * @package   ksfraser\FrontAccounting\Documents
 * @author    Kevin Fraser
 * @version   2.4.19-0
 * @license   GPL-3.0
 *
 * @BABOK Related: FR-DOC-001
 *
 * @hook      install           Called when module is installed.
 * @hook      uninstall         Called when module is deleted.
 * @hook      install_options   Register pages under Documents app.
 * @hook      install_access    Define security areas.
 * @hook      activate_extension Install SQL schema.
 */

define('SS_ksf_FA_Documents', 151 << 8);

$autoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use Ksfraser\FrontAccounting\Documents\App\DocumentsAppShell;
use Ksfraser\FrontAccounting\Documents\App\DocumentsFrontController;

class hooks_ksf_FA_Documents extends hooks
{
    use \Ksfraser\Traits\HookQueryProviderTrait;

    var $module_name = 'ksf_FA_Documents';
    var $version     = '2.4.19-0';

    protected function _getAdvertisedValues(): array
    {
        return [
            'documents.hooks_version' => '1.0',
            'documents.module_version' => '1.0.0',
            'documents.features' => ['documents', 'attachments', 'acknowledgments', 'versions', 'links'],
        ];
    }

    public function getModuleConstants(&$data, $opts = null)
    {
        $constants = [
            'KSF_DOCUMENTS_MODULE_NAME' => $this->module_name,
            'KSF_DOCUMENTS_CAPABILITIES' => 'document_create,document_update,document_delete,document_search',
        ];
        $data['constants'] = $constants;
        $data['_module'] = $this->module_name;
        return $constants;
    }

    public function getModuleCapabilities(&$data, $opts = null)
    {
        $capabilities = [
            'document_create' => [
                'description' => 'Create documents',
                'methods' => ['CREATE_DOCUMENT', 'DOCUMENT_CREATED'],
            ],
            'document_update' => [
                'description' => 'Update documents',
                'methods' => ['UPDATE_DOCUMENT', 'DOCUMENT_UPDATED'],
            ],
            'document_delete' => [
                'description' => 'Delete documents',
                'methods' => ['DELETE_DOCUMENT', 'DOCUMENT_DELETED'],
            ],
            'document_search' => [
                'description' => 'Search documents',
                'methods' => ['SEARCH_DOCUMENT', 'DOCUMENT_FOUND'],
            ],
        ];
        $data['capabilities'] = $capabilities;
        $data['_module'] = $this->module_name;
        return $capabilities;
    }

    public function hasCapability(&$data, $opts = null)
    {
        $cap = $opts['capability'] ?? null;
        if ($cap === null) {
            return false;
        }
        $available = array_keys($this->getModuleCapabilities($data)['capabilities'] ?? []);
        return in_array($cap, $available, true);
    }

    public function respondToCapabilityRequest(&$data, $opts = null)
    {
        $cap = $opts['capability'] ?? null;
        if ($cap === null || !$this->hasCapability($data, $opts)) {
            $data['supported'] = false;
            return;
        }
        $data['supported'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Register module menus under Documents application
     */
    function install_options($app) {
        if ($app->id == 'orders' && method_exists($this, 'add_module_app')) {
            $this->add_module_app('documents', _("Documents"), 'modules/ksf_FA_Documents/index.php?view=documents', SA_ksf_FA_DocumentsVIEW);
            $this->add_module_app('document_attachments', _("Attachments"), 'modules/ksf_FA_Documents/index.php?view=attachments', SA_ksf_FA_DocumentsVIEW);
            $this->add_module_app('document_acknowledgments', _("Acknowledgments"), 'modules/ksf_FA_Documents/index.php?view=acknowledgments', SA_ksf_FA_DocumentsVIEW);
            $this->add_module_app('document_versions', _("Versions"), 'modules/ksf_FA_Documents/index.php?view=versions', SA_ksf_FA_DocumentsVIEW);
            $this->add_module_app('document_links', _("Links"), 'modules/ksf_FA_Documents/index.php?view=links', SA_ksf_FA_DocumentsVIEW);
            $this->add_module_app('document_config', _("Documents Config"), 'modules/ksf_FA_Documents/index.php?view=config', SA_ksf_FA_DocumentsMANAGE);
        }
    }

    /**
     * Define security permission areas
     */
    function install_access() {
        $security_sections[SS_ksf_FA_Documents] = _("Documents");
        $security_areas['SA_ksf_FA_DocumentsVIEW'] = array(
            SS_ksf_FA_Documents | 1,
            _("View Documents")
        );
        $security_areas['SA_ksf_FA_DocumentsMANAGE'] = array(
            SS_ksf_FA_Documents | 2,
            _("Manage Documents Config")
        );
        return array($security_areas, $security_sections);
    }

    /**
     * Install database schema on activation
     */
    function activate_extension($company, $check_only=true) {
        $this->ensure_composer_dependencies();

        if (!$this->ksf_fa_common_available()) {
            display_warning(_('Documents requires the ksf-fa-common Composer package. Run composer install, then re-activate Documents.'));
            return false;
        }

        if (!$check_only) {
            $this->install_common_table('ksf_modules.sql');
        }

        if (file_exists(dirname(__FILE__) . '/sql/install.sql')) {
            $updates = array(
                'ksf_documents.sql'             => array('ksf_documents'),
                'ksf_documents_attachments.sql' => array('ksf_documents_attachments'),
                'ksf_documents_acknowledgments.sql'=> array('ksf_documents_acknowledgments'),
                'ksf_documents_versions.sql'    => array('ksf_documents_versions'),
                'ksf_documents_links.sql'       => array('ksf_documents_links'),
            );
            return $this->update_databases($company, $updates, $check_only);
        }

        return true;
    }

    private function ksf_fa_common_available()
    {
        $autoload = dirname(__FILE__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            return false;
        }
        return class_exists('ksfraser\\FrontAccounting\\Common\\Utils\\SchemaInstaller', false);
    }

    private function install_common_table(string $tableFile): bool
    {
        $vendorDir = dirname(__FILE__) . '/vendor/ksfraser/ksf-fa-common';
        $sqlPath   = $vendorDir . '/sql/' . $tableFile;

        if (!file_exists($sqlPath)) {
            error_log('KSF Module: common table SQL not found: ' . $sqlPath);
            return false;
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            return false;
        }

        $sql = preg_replace('/--[^\n]*/', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $statements = array_map('trim', explode(';', $sql));

        $ok = true;
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') {
                continue;
            }
            if (!db_query($stmt, 'Common table install failed: ' . substr($stmt, 0, 80))) {
                $ok = false;
            }
        }
        return $ok;
    }

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

        chdir($module_dir);
        $output = array();
        $return_code = 0;
        exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
        if ($return_code !== 0) {
            error_log('KSF Module: composer install failed: ' . implode("\n", $output));
        }
    }
}
