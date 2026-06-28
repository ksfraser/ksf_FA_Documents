<?php
declare(strict_types=1);

namespace Ksfraser\Documents\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DocumentsDbTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TB_PREF')) {
            define('TB_PREF', '0_');
        }
    }

    // --- Multi-link tests ---

    public function testLinkDocumentFunctionExists(): void
    {
        $this->assertTrue(function_exists('link_document'));
    }

    public function testUnlinkDocumentFunctionExists(): void
    {
        $this->assertTrue(function_exists('unlink_document'));
    }

    public function testGetDocumentLinkedEntitiesFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_document_linked_entities'));
    }

    // --- ACL tests ---

    public function testDocumentCanViewFunctionExists(): void
    {
        $this->assertTrue(function_exists('document_can_view'));
    }

    public function testDocumentCanEditFunctionExists(): void
    {
        $this->assertTrue(function_exists('document_can_edit'));
    }

    // --- Shared attachment tests ---

    public function testAddDocumentSharedAttachmentFunctionExists(): void
    {
        $this->assertTrue(function_exists('add_document_shared_attachment'));
    }

    public function testGetDocumentSharedAttachmentsFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_document_shared_attachments'));
    }

    // --- Existing functions still work ---

    public function testCreateDocumentFunctionExists(): void
    {
        $this->assertTrue(function_exists('create_document'));
    }

    public function testGetDocumentFunctionExists(): void
    {
        $this->assertTrue(function_exists('get_document'));
    }

    public function testUpdateDocumentFunctionExists(): void
    {
        $this->assertTrue(function_exists('update_document'));
    }
}
