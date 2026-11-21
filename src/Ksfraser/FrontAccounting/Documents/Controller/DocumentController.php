<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Documents\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\Documents\Service\DocumentService;

/**
 * DocumentController — controller SRP for Documents tab.
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */
class DocumentController extends AbstractTabController
{
    private $service;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new DocumentService();
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'document',
            'table'       => '0_ksf_documents',
            'label'       => 'Document',
            'labelPlural' => 'Documents',
            'hookPrefix'  => 'document',
            'pk'          => 'id',
            'fields'      => [],
            'fk_ddls'     => [],
            'ddlHooks'    => [],
            'tableSettings' => [],
        ];
    }

    protected function listRows(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return array_slice($this->service->searchDocuments([]), $offset, $perPage);
    }

    protected function countRows(): int
    {
        return count($this->service->searchDocuments([]));
    }

    protected function findRecord(string $pk): ?array
    {
        $dto = $this->service->findDocumentById((int)$pk);
        return $dto ? $dto->toArray() : null;
    }

    protected function createRecord(array $data): int
    {
        $dto = $this->service->createDocument($data);
        return $dto->id;
    }

    protected function updateRecord(string $pk, array $data): void
    {
        $this->service->updateDocument((int)$pk, $data);
    }

    protected function deleteRecord(string $pk): void
    {
        $this->service->deleteDocument((int)$pk);
    }

    protected function fkOptions(): array
    {
        return [];
    }
}
