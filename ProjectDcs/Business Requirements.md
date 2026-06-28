# Business Requirements - ksf_FA_Documents

## Document Information
- **Module**: ksf_FA_Documents
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated
- **Author**: KSFII Development Team

---

## 1. Project Overview

ksf_FA_Documents is the FrontAccounting adapter for ksf_Documents, providing document management within FA. The module enables storage, versioning, multi-entity linking, access control, acknowledgment tracking, and shared attachment management.

---

## 2. Adapter Pattern

```
ksf_Documents (Business Logic)
    ↓
ksf_FA_Documents (FA Adapter)
    ↓
    FrontAccounting UI
```

---

## 3. Business Requirements

### BR-DOC-001: Document Storage and Retrieval
**Description**: The system shall allow users to upload, store, and retrieve documents with metadata (title, type, version, status, expiry date).

**Rationale**: Central document repository for company policies, contracts, forms, and training materials.

**Priority**: High

---

### BR-DOC-002: Document Versioning
**Description**: The system shall maintain version history for documents, preserving all prior versions when a new version is uploaded.

**Rationale**: Regulatory compliance and audit trail for document changes.

**Priority**: High

---

### BR-DOC-003: Multi-Entity Linking (NEW)
**Description**: The system shall allow a single document to be linked to multiple entities across different FA modules (e.g., a company policy document linked to project tasks, customer quotes, and employee training records simultaneously).

**Rationale**: Business documents often span multiple operational contexts. A safety policy may apply to a project task, a customer contract, and an employee training course. The old single entity_type/entity_id pattern was insufficient.

**Acceptance Criteria**:
- A document can be linked to 0, 1, or many entities
- Entity types include customer, project, task, employee, training_course, quote, supplier
- Links can be added and removed independently
- Querying documents by any entity returns all linked documents
- Migration from legacy entity_type/entity_id pattern preserves existing links

**Priority**: High

---

### BR-DOC-004: Access Control (NEW)
**Description**: The system shall enforce document-level access control based on document owner and assigned security group, layered on top of FrontAccounting's existing RBAC.

**Rationale**: Documents may contain sensitive information that should only be accessible to specific users or groups. Owner-based ownership ensures users see their own documents by default, while group assignments enable team-level access.

**Acceptance Criteria**:
- Each document has an `owner` (FK to FA users) and `group_id` (FK to FA security groups)
- Owner always has view and edit access to their own documents
- Users in the same group as a document can view it; group members with DOC_MANAGE can edit
- Users with DOC_ADMIN bypass all owner/group checks
- New documents default to current user as owner and their group as group_id

**Priority**: High

---

### BR-DOC-005: Shared Attachment Service (NEW)
**Description**: The system shall use the ksf_FA_Attachments module as the shared attachment service for new file uploads, while maintaining read-only backward compatibility with the legacy fa_document_attachments table.

**Rationale**: Eliminate redundant attachment storage across modules by centralizing file management in ksf_FA_Attachments, while preserving existing document attachments.

**Acceptance Criteria**:
- New document attachments are stored in `fa_attachments` with `source_type='document'`
- Legacy `fa_document_attachments` data remains accessible (read-only)
- Each document version can have its own attachments
- Attachment operations (upload, list, delete) are delegated to AttachmentOperationsTrait

**Priority**: High

---

### BR-DOC-006: Acknowledgment Workflow
**Description**: The system shall support document acknowledgment workflows where employees must acknowledge receipt and understanding of documents.

**Rationale**: Compliance with company policies and regulatory requirements for document acknowledgment.

**Priority**: High

---

### BR-DOC-007: Expiry Tracking and Reminders
**Description**: The system shall track document expiry dates and provide reminders for documents approaching or past expiry.

**Rationale**: Ensure documents are reviewed and renewed before they expire, maintaining compliance.

**Priority**: High

---

### BR-DOC-008: Note vs Document Distinction
**Description**: The system shall distinguish between formal "Documents" (versioned, may require acknowledgment) and informal "Notes" (no versioning, no acknowledgment requirement).

**Rationale**: Not all content needs the full document workflow. Notes are lightweight entries for quick reference.

**Priority**: Medium

---

## 4. Integration Points

| Component | Description |
|-----------|-------------|
| hooks.php | Module registration, lifecycle hooks |
| documents.php | UI page routing |
| ksf_FA_Attachments | Shared attachment service (dependency) |
| ksf_Documents | Core business logic |

---

## 5. Dependencies

| Module | Version | Purpose |
|--------|---------|---------|
| FrontAccounting | 2.4+ | Host framework |
| ksf_Documents | 1.0+ | Core business logic |
| ksf_FA_Attachments | 2.0+ | Shared attachment service |

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
