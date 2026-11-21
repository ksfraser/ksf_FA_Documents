<?php
/**
 * ksf_FA_Documents — single front controller.
 *
 * URL shape:
 *   index.php                                default tab (documents list)
 *   index.php?view=documents                 read a tab
 *   index.php?tab=documents                  alias
 *   index.php?view=documents&page=3          tab + paging
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */

chdir(__DIR__);

if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
}

$path_to_root = "../..";

$frontController = new \Ksfraser\FrontAccounting\Documents\App\DocumentsFrontController(
    new \Ksfraser\FrontAccounting\Documents\App\DocumentsAppShell(),
    'SA_ksf_FA_DocumentsVIEW',
    $path_to_root
);

$page_security = $frontController->getSecurityArea();
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");

$frontController->run();
