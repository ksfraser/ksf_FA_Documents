<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Documents\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\Documents\Service\DocumentAttachmentService;

/**
 * AttachmentController — controller SRP for Attachments tab.
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */
class AttachmentController extends AbstractTabController
{
    private $service;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new DocumentAttachmentService();
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'attachment',
            'table'       => '0_ksf_documents_attachments',
            'label'       => 'Attachment',
            'labelPlural' => 'Attachments',
            'hookPrefix'  => 'attachment',
            'pk'          => 'id',
            'fields'      => [],
            'fk_ddls'     => [],
            'ddlHooks'    => [],
            'tableSettings' => [],
        ];
    }

    protected function listRows(int $page, int $perPage): array
    {
        $docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
        $offset = ($page - 1) * $perPage;
        return array_slice($this->service->getAttachmentsForDocument($docId), $offset, $perPage);
    }

    protected function countRows(): int
    {
        $docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
        return count($this->service->getAttachmentsForDocument($docId));
    }

    protected function findRecord(string $pk): ?array
    {
        $dto = $this->service->findAttachmentById((int)$pk);
        return $dto ? $dto->toArray() : null;
    }

    protected function createRecord(array $data): int
    {
        $dto = $this->service->createAttachment($data);
        return $dto->id;
    }

    protected function updateRecord(string $pk, array $data): void
    {
        $this->service->updateAttachment((int)$pk, $data);
    }

    protected function deleteRecord(string $pk): void
    {
        $this->service->deleteAttachment((int)$pk);
    }

    protected function fkOptions(): array
    {
        return [];
    }
}
