<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Documents\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

/**
 * DocumentsFrontController — single front controller.
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */
class DocumentsFrontController extends AbstractFrontController
{
    public function __construct(
        DocumentsAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
