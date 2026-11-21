<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Documents\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\Documents\Service\DocumentLinkService;

/**
 * LinkController — controller SRP for Links tab.
 *
 * @package ksf_FA_Documents
 * @since   1.0.0
 */
class LinkController extends AbstractTabController
{
    private $service;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new DocumentLinkService();
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'link',
            'table'       => '0_ksf_documents_links',
            'label'       => 'Link',
            'labelPlural' => 'Links',
            'hookPrefix'  => 'link',
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
        return array_slice($this->service->getLinksForDocument($docId), $offset, $perPage);
    }

    protected function countRows(): int
    {
        $docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
        return count($this->service->getLinksForDocument($docId));
    }

    protected function findRecord(string $pk): ?array
    {
        $dto = $this->service->findLinkById((int)$pk);
        return $dto ? $dto->toArray() : null;
    }

    protected function createRecord(array $data): int
    {
        $dto = $this->service->createLink($data);
        return $dto->id;
    }

    protected function updateRecord(string $pk, array $data): void
    {
        $this->service->updateLink((int)$pk, $data);
    }

    protected function deleteRecord(string $pk): void
    {
        $this->service->deleteLink((int)$pk);
    }

    protected function fkOptions(): array
    {
        return [];
    }
}
