<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Documents\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use Ksfraser\FrontAccounting\Documents\Controller\DocumentController;
use Ksfraser\FrontAccounting\Documents\Controller\AttachmentController;
use Ksfraser\FrontAccounting\Documents\Controller\AcknowledgmentController;
use Ksfraser\FrontAccounting\Documents\Controller\VersionController;
use Ksfraser\FrontAccounting\Documents\Controller\LinkController;

/**
 * DocumentsAppShell — application shell SRP.
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */
class DocumentsAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('documents', 'index.php', 'view', 'documents');

        $this->add_module(_("Documents"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_Documents/index.php',
            'view',
            ''
        );

        $menu->addItem('documents', _("Documents"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsVIEW');
        $menu->addItem('attachments', _("Attachments"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsVIEW');
        $menu->addItem('acknowledgments', _("Acknowledgments"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsVIEW');
        $menu->addItem('versions', _("Versions"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsVIEW');
        $menu->addItem('links', _("Links"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsVIEW');
        $menu->addItem('config', _("Documents Config"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_DocumentsMANAGE');

        $this->add_extensions();
    }
}
